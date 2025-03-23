package com.example.project.projectPrm;

import android.annotation.SuppressLint;
import android.os.Bundle;
import android.util.Log;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.project.R;

import java.net.URLEncoder;
import java.nio.charset.StandardCharsets;
import java.security.MessageDigest;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.TimeZone;
import java.util.TreeMap;

import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;

public class VNPayActivity extends AppCompatActivity {
    private WebView webView;
    private static final String VNPAY_URL = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    private static final String MERCHANT_ID = "2QXUI4J4"; // Terminal ID test
    private static final String HASH_SECRET = "NYYZTXVDGFWGTVBZDZDRSYRIIWBQNXCN"; // Secret key test
    private static final String TAG = "VNPayActivity";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_vnpay);

        double amount = getIntent().getDoubleExtra("amount", 0);
        initWebView();
        loadVNPayPaymentUrl(amount);
    }

    @SuppressLint("SetJavaScriptEnabled")
    private void initWebView() {
        webView = findViewById(R.id.webView);
        webView.getSettings().setJavaScriptEnabled(true);
        webView.getSettings().setDomStorageEnabled(true);
        webView.getSettings().setLoadWithOverviewMode(true);
        webView.getSettings().setUseWideViewPort(true);
        webView.getSettings().setSupportZoom(true);
        webView.getSettings().setBuiltInZoomControls(true);
        webView.getSettings().setDisplayZoomControls(false);
        
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                view.loadUrl(url);
                Log.d(TAG, "Loading URL: " + url);
                return true;
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                Log.d(TAG, "Page finished loading: " + url);
                
                if (url.contains("vnp_ResponseCode=00")) {
                    // Thanh toán thành công
                    Toast.makeText(VNPayActivity.this, "Thanh toán thành công!", Toast.LENGTH_LONG).show();
                    setResult(RESULT_OK);
                    finish();
                } else if (url.contains("vnp_ResponseCode")) {
                    // Thanh toán thất bại hoặc bị hủy
                    Toast.makeText(VNPayActivity.this, "Thanh toán không thành công hoặc bị hủy", Toast.LENGTH_LONG).show();
                    setResult(RESULT_CANCELED);
                    finish();
                }
            }
        });
    }

    private void loadVNPayPaymentUrl(double amount) {
        try {
            String vnpVersion = "2.1.0";
            String vnpCommand = "pay";
            String orderType = "other";
            String vnpTxnRef = generateTransactionRef();
            String vnpIpAddr = "127.0.0.1";
            String vnpCreateDate = generateCreateDate();
            String locale = "vn";
            
            // Format số tiền theo yêu cầu của VNPAY (nhân 100)
            long amountInVND = Math.round(amount * 100);
            
            Map<String, String> vnpParams = new TreeMap<>();
            vnpParams.put("vnp_Amount", String.valueOf(amountInVND));
            vnpParams.put("vnp_Command", vnpCommand);
            vnpParams.put("vnp_CreateDate", vnpCreateDate);
            vnpParams.put("vnp_CurrCode", "VND");
            vnpParams.put("vnp_IpAddr", vnpIpAddr);
            vnpParams.put("vnp_Locale", locale);
            vnpParams.put("vnp_OrderInfo", "Thanh toan don hang " + vnpTxnRef);
            vnpParams.put("vnp_OrderType", orderType);
            vnpParams.put("vnp_ReturnUrl", "https://sandbox.vnpayment.vn/tryitnow/Home/VnPayReturn");
            vnpParams.put("vnp_TmnCode", MERCHANT_ID);
            vnpParams.put("vnp_TxnRef", vnpTxnRef);
            vnpParams.put("vnp_Version", vnpVersion);

            StringBuilder hashData = new StringBuilder();
            StringBuilder query = new StringBuilder();
            
            // Tạo chuỗi hash data và query string
            for (Map.Entry<String, String> entry : vnpParams.entrySet()) {
                String fieldName = entry.getKey();
                String fieldValue = entry.getValue();
                
                if (fieldValue != null && !fieldValue.isEmpty()) {
                    // Build hash data (không encode)
                    if (hashData.length() > 0) {
                        hashData.append('&');
                    }
                    hashData.append(fieldName).append('=').append(fieldValue);
                    
                    // Build query (encode)
                    if (query.length() > 0) {
                        query.append('&');
                    }
                    query.append(URLEncoder.encode(fieldName, "UTF-8"))
                         .append('=')
                         .append(URLEncoder.encode(fieldValue, "UTF-8"));
                }
            }

            // Tạo HMAC SHA512 signature
            String secureHash = hmacSHA512(HASH_SECRET, hashData.toString());
            
            // Thêm signature vào URL
            if (query.length() > 0) {
                query.append('&');
            }
            query.append("vnp_SecureHash=").append(secureHash);

            // Tạo URL hoàn chỉnh
            String paymentUrl = VNPAY_URL + "?" + query.toString();
            
            // Log để debug
            Log.d(TAG, "Original Amount: " + amount);
            Log.d(TAG, "Amount in VND (x100): " + amountInVND);
            Log.d(TAG, "TxnRef: " + vnpTxnRef);
            Log.d(TAG, "CreateDate: " + vnpCreateDate);
            Log.d(TAG, "Hash Data: " + hashData.toString());
            Log.d(TAG, "Secure Hash: " + secureHash);
            Log.d(TAG, "Final URL: " + paymentUrl);

            // Load URL trong WebView
            webView.loadUrl(paymentUrl);

        } catch (Exception e) {
            Log.e(TAG, "Error creating payment URL: " + e.getMessage());
            e.printStackTrace();
            Toast.makeText(this, "Lỗi tạo URL thanh toán: " + e.getMessage(), Toast.LENGTH_LONG).show();
            setResult(RESULT_CANCELED);
            finish();
        }
    }

    private String hmacSHA512(String key, String data) {
        try {
            byte[] hmacKeyBytes = key.getBytes("UTF-8");
            SecretKeySpec secretKey = new SecretKeySpec(hmacKeyBytes, "HmacSHA512");
            Mac hmacSha512 = Mac.getInstance("HmacSHA512");
            hmacSha512.init(secretKey);
            byte[] dataBytes = data.getBytes("UTF-8");
            byte[] result = hmacSha512.doFinal(dataBytes);
            StringBuilder sb = new StringBuilder(2 * result.length);
            for (byte b : result) {
                sb.append(String.format("%02x", b & 0xff));
            }
            return sb.toString().toUpperCase();
        } catch (Exception ex) {
            return "";
        }
    }

    private String generateTransactionRef() {
        SimpleDateFormat sdf = new SimpleDateFormat("yyyyMMddHHmmss");
        String timestamp = sdf.format(new Date());
        return timestamp + "_" + System.currentTimeMillis() % 1000;
    }

    private String generateCreateDate() {
        SimpleDateFormat sdf = new SimpleDateFormat("yyyyMMddHHmmss");
        sdf.setTimeZone(TimeZone.getTimeZone("GMT+7"));
        return sdf.format(new Date());
    }

    @Override
    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
        } else {
            setResult(RESULT_CANCELED);
            super.onBackPressed();
        }
    }
} 