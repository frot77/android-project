package com.example.project.projectPrm;

import android.annotation.SuppressLint;
import android.os.Bundle;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.project.R;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

public class VNPayActivity extends AppCompatActivity {
    private WebView webView;
    private static final String VNPAY_URL = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    private static final String MERCHANT_ID = "YOUR_MERCHANT_ID"; // Thay bằng Merchant ID của bạn
    private static final String HASH_SECRET = "YOUR_HASH_SECRET"; // Thay bằng Hash Secret của bạn

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
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                if (url.contains("vnp_ResponseCode=00")) {
                    // Thanh toán thành công
                    setResult(RESULT_OK);
                    finish();
                    return true;
                } else if (url.contains("vnp_ResponseCode")) {
                    // Thanh toán thất bại hoặc bị hủy
                    setResult(RESULT_CANCELED);
                    finish();
                    return true;
                }
                return false;
            }
        });
    }

    private void loadVNPayPaymentUrl(double amount) {
        String vnpVersion = "2.1.0";
        String vnpCommand = "pay";
        String orderType = "other";
        String vnpTxnRef = generateTransactionRef();
        String vnpIpAddr = "127.0.0.1";
        String vnpCreateDate = getCurrentDateTimeString();
        String locale = "vn";
        long amountInVND = (long) (amount * 100); // Chuyển đổi sang số tiền theo format của VNPAY (x100)

        StringBuilder queryUrl = new StringBuilder(VNPAY_URL);
        queryUrl.append("?vnp_Version=").append(vnpVersion);
        queryUrl.append("&vnp_Command=").append(vnpCommand);
        queryUrl.append("&vnp_TmnCode=").append(MERCHANT_ID);
        queryUrl.append("&vnp_Amount=").append(amountInVND);
        queryUrl.append("&vnp_CurrCode=VND");
        queryUrl.append("&vnp_TxnRef=").append(vnpTxnRef);
        queryUrl.append("&vnp_OrderInfo=Thanh toan don hang: ").append(vnpTxnRef);
        queryUrl.append("&vnp_OrderType=").append(orderType);
        queryUrl.append("&vnp_Locale=").append(locale);
        queryUrl.append("&vnp_ReturnUrl=").append("https://your-domain.com/vnpay_return.php"); // Thay bằng URL return của bạn
        queryUrl.append("&vnp_IpAddr=").append(vnpIpAddr);
        queryUrl.append("&vnp_CreateDate=").append(vnpCreateDate);

        // TODO: Thêm vnp_SecureHash - Cần implement theo tài liệu VNPAY

        webView.loadUrl(queryUrl.toString());
    }

    private String generateTransactionRef() {
        SimpleDateFormat sdf = new SimpleDateFormat("yyyyMMddHHmmss", Locale.getDefault());
        return sdf.format(new Date());
    }

    private String getCurrentDateTimeString() {
        SimpleDateFormat sdf = new SimpleDateFormat("yyyyMMddHHmmss", Locale.getDefault());
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