package com.example.project.projectPrm;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.project.R;

import java.net.URLEncoder;
import java.nio.charset.StandardCharsets;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.TimeZone;

import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;

public class VNPayActivity extends AppCompatActivity {
    private static final String TAG = "VNPayActivity";
    private static final String MERCHANT_ID = "9KUCMY4C"; // Terminal ID / Mã Website VNPAY
    private static final String HASH_SECRET = "YWBCBPZVIWQYRISVYSEITNMQTRQYVWIK"; // Secret Key VNPAY
    private static final String VNP_PAY_URL = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    private static final String VNP_RETURN_URL = "mobile://vnpay.return"; // URL scheme cho mobile app

    private WebView webView;
    private double amount;
    private String orderId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_vnpay);

        amount = getIntent().getDoubleExtra("amount", 0);
        orderId = getIntent().getStringExtra("orderId");

        webView = findViewById(R.id.webView);
        webView.getSettings().setJavaScriptEnabled(true);
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                if (url.startsWith("mobile://vnpay.return")) {
                    // Xử lý kết quả thanh toán
                    handlePaymentResult(url);
                    return true;
                }
                view.loadUrl(url);
                return true;
            }
        });

        loadVNPayPaymentUrl(amount);
    }

    private void handlePaymentResult(String url) {
        try {
            String[] urlParts = url.split("\\?");
            if (urlParts.length > 1) {
                String[] params = urlParts[1].split("&");
                Map<String, String> result = new HashMap<>();
                for (String param : params) {
                    String[] keyValue = param.split("=");
                    if (keyValue.length == 2) {
                        result.put(keyValue[0], keyValue[1]);
                    }
                }

                String responseCode = result.get("vnp_ResponseCode");
                if ("00".equals(responseCode)) {
                    Toast.makeText(this, "Thanh toán thành công!", Toast.LENGTH_SHORT).show();
                    // Chuyển về màn hình chính
                    Intent intent = new Intent(this, ProductMainActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    startActivity(intent);
                } else {
                    Toast.makeText(this, "Thanh toán không thành công hoặc bị hủy", Toast.LENGTH_SHORT).show();
                    finish();
                }
            }
        } catch (Exception e) {
            Log.e(TAG, "Error handling payment result", e);
            Toast.makeText(this, "Có lỗi xảy ra khi xử lý kết quả thanh toán", Toast.LENGTH_SHORT).show();
            finish();
        }
    }

    private void loadVNPayPaymentUrl(double amount) {
        try {
            Map<String, String> vnp_Params = new HashMap<>();
            
            // Thông tin giao dịch
            String vnp_TxnRef = String.valueOf(System.currentTimeMillis() % 100000000);
            String vnp_CreateDate = generateCreateDate();
            long amountInVND = (long)(amount * 100);
            
            // Thêm thông tin
            vnp_Params.put("vnp_Version", "2.1.0");
            vnp_Params.put("vnp_Command", "pay");
            vnp_Params.put("vnp_TmnCode", MERCHANT_ID);
            vnp_Params.put("vnp_Amount", String.valueOf(amountInVND));
            vnp_Params.put("vnp_BankCode", "NCB");
            vnp_Params.put("vnp_CreateDate", vnp_CreateDate);
            vnp_Params.put("vnp_CurrCode", "VND");
            vnp_Params.put("vnp_IpAddr", "127.0.0.1");
            vnp_Params.put("vnp_Locale", "vn");
            vnp_Params.put("vnp_OrderInfo", "Thanh toan don hang:" + vnp_TxnRef);
            vnp_Params.put("vnp_OrderType", "other");
            vnp_Params.put("vnp_ReturnUrl", "https://sandbox.vnpayment.vn/tryitnow/Home/VnPayReturn");
            vnp_Params.put("vnp_TxnRef", vnp_TxnRef);

            // Sắp xếp các tham số theo thứ tự a-z và tạo chuỗi hash data
            List<String> fieldNames = new ArrayList<>(vnp_Params.keySet());
            Collections.sort(fieldNames);
            StringBuilder hashData = new StringBuilder();
            
            Iterator<String> itr = fieldNames.iterator();
            while (itr.hasNext()) {
                String fieldName = itr.next();
                String fieldValue = vnp_Params.get(fieldName);
                if ((fieldValue != null) && (fieldValue.length() > 0)) {
                    hashData.append(fieldName);
                    hashData.append('=');
                    hashData.append(URLEncoder.encode(fieldValue, StandardCharsets.US_ASCII.toString()));
                    if (itr.hasNext()) {
                        hashData.append('&');
                    }
                }
            }

            String vnp_SecureHash = hmacSHA512(HASH_SECRET, hashData.toString());

            // Tạo URL thanh toán
            String queryUrl = hashData.toString();
            String paymentUrl = VNP_PAY_URL + "?" + queryUrl + "&vnp_SecureHash=" + vnp_SecureHash;

            Log.d(TAG, "Hash Data: " + hashData.toString());
            Log.d(TAG, "Secure Hash: " + vnp_SecureHash);
            Log.d(TAG, "Payment URL: " + paymentUrl);

            webView.loadUrl(paymentUrl);

        } catch (Exception e) {
            Log.e(TAG, "Error creating payment URL", e);
            Toast.makeText(this, "Có lỗi xảy ra khi tạo URL thanh toán", Toast.LENGTH_SHORT).show();
        }
    }

    private String generateCreateDate() {
        Calendar cld = Calendar.getInstance(TimeZone.getTimeZone("Etc/GMT+7"));
        SimpleDateFormat formatter = new SimpleDateFormat("yyyyMMddHHmmss");
        return formatter.format(cld.getTime());
    }

    private String hmacSHA512(String key, String data) {
        try {
            Mac sha512Hmac = Mac.getInstance("HmacSHA512");
            byte[] hmacKeyBytes = key.getBytes("UTF-8");
            SecretKeySpec secretKey = new SecretKeySpec(hmacKeyBytes, "HmacSHA512");
            sha512Hmac.init(secretKey);
            byte[] dataBytes = data.getBytes("UTF-8");
            byte[] result = sha512Hmac.doFinal(dataBytes);
            StringBuilder sb = new StringBuilder(2 * result.length);
            for (byte b : result) {
                sb.append(String.format("%02x", b & 0xff));
            }
            return sb.toString().toUpperCase();
        } catch (Exception e) {
            Log.e(TAG, "Error generating HMAC", e);
            return "";
        }
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