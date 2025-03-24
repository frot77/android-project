package com.example.project.projectPrm;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.project.R;
import com.example.project.projectPrm.Response.OrderResponse;
import com.example.project.projectPrm.Response.Product;
import com.example.project.projectPrm.api.InterfaceOrder;
import com.google.gson.Gson;

import java.text.NumberFormat;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class CheckoutActivity extends AppCompatActivity {
    private EditText etFullName, etPhone, etAddress;
    private TextView tvTotalAmount;
    private RadioGroup rgPaymentMethod;
    private Button btnConfirmOrder;
    private double totalAmount = 0;
    private static final String BASE_URL = "http://10.33.54.186/";
    private InterfaceOrder interfaceOrder;

    private static final int VNPAY_REQUEST_CODE = 1000;
    private static final int VNPAY_PAYMENT_REQUEST = 1001;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_checkout);

        // Khởi tạo Retrofit
        Retrofit retrofit = new Retrofit.Builder()
                .baseUrl(BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
        interfaceOrder = retrofit.create(InterfaceOrder.class);

        initViews();
        calculateTotal();
        setupListeners();
    }

    private void initViews() {
        etFullName = findViewById(R.id.etFullName);
        etPhone = findViewById(R.id.etPhone);
        etAddress = findViewById(R.id.etAddress);
        tvTotalAmount = findViewById(R.id.tvTotalAmount);
        rgPaymentMethod = findViewById(R.id.rgPaymentMethod);
        btnConfirmOrder = findViewById(R.id.btnConfirmOrder);
    }

    private void calculateTotal() {
        for (Product product : CartManager.getInstance().getCartItems().keySet()) {
            int quantity = CartManager.getInstance().getCartItems().get(product);
            totalAmount += Double.parseDouble(product.getPrice()) * quantity;
        }
        String formattedTotal = NumberFormat.getNumberInstance(Locale.US).format(totalAmount) + " VND";
        tvTotalAmount.setText("Tổng tiền: " + formattedTotal);
    }

    private void setupListeners() {
        btnConfirmOrder.setOnClickListener(v -> {
            if (validateInput()) {
                processOrder();
            }
        });
    }

    private boolean validateInput() {
        if (etFullName.getText().toString().trim().isEmpty()) {
            etFullName.setError("Vui lòng nhập họ tên");
            return false;
        }
        if (etPhone.getText().toString().trim().isEmpty()) {
            etPhone.setError("Vui lòng nhập số điện thoại");
            return false;
        }
        if (etAddress.getText().toString().trim().isEmpty()) {
            etAddress.setError("Vui lòng nhập địa chỉ");
            return false;
        }
        return true;
    }

    private void processOrder() {
        // Lấy thông tin đơn hàng
        String fullName = etFullName.getText().toString().trim();
        String phone = etPhone.getText().toString().trim();
        String address = etAddress.getText().toString().trim();
        
        // Kiểm tra phương thức thanh toán
        if (rgPaymentMethod.getCheckedRadioButtonId() == R.id.rbVNPay) {
            // Chuyển sang thanh toán VNPAY
            processVNPayPayment();
        } else {
            // Thanh toán COD
            processCODPayment(fullName, phone, address);
        }
    }

    private void processVNPayPayment() {
        // Số tiền test: 50,000 VND
        double amount = 50000;
        
        Intent intent = new Intent(this, VNPayActivity.class);
        intent.putExtra("amount", amount);
        startActivityForResult(intent, VNPAY_PAYMENT_REQUEST);
    }

    private void processCODPayment(String fullName, String phone, String address) {
        try {
            // Lấy danh sách sản phẩm từ giỏ hàng
            HashMap<Product, Integer> cartItems =(HashMap<Product, Integer>) CartManager.getInstance().getCartItems();
            
            // Tạo danh sách items bằng Gson
            List<Map<String, String>> items = new ArrayList<>();
            
            for (Map.Entry<Product, Integer> entry : cartItems.entrySet()) {
                Product product = entry.getKey();
                int quantity = entry.getValue();
                
                Map<String, String> item = new HashMap<>();
                item.put("product_id", product.getId());
                item.put("quantity", String.valueOf(quantity));
                item.put("price", product.getPrice());
                items.add(item);
                
                // Log từng sản phẩm
                Log.d("ORDER_DEBUG", "Product: " + product.getId() + 
                    ", Quantity: " + quantity + 
                    ", Price: " + product.getPrice());
            }
            
            // Chuyển list thành JSON string
            Gson gson = new Gson();
            String itemsJson = gson.toJson(items);
            
            // Log JSON và thông tin đơn hàng
            Log.d("ORDER_DEBUG", "Items JSON: " + itemsJson);
            Log.d("ORDER_DEBUG", "Full Name: " + fullName);
            Log.d("ORDER_DEBUG", "Phone: " + phone);
            Log.d("ORDER_DEBUG", "Address: " + address);
            
            String userId = getSharedPreferences("user_prefs", MODE_PRIVATE).getString("user_id", "");
            Log.d("ORDER_DEBUG", "UserID: " + userId);

            // Gọi API tạo đơn hàng
            Call<OrderResponse> call = interfaceOrder.createOrder(
                userId,
                fullName,
                phone,
                address,
                "COD",
                itemsJson
            );

            call.enqueue(new Callback<OrderResponse>() {
                @Override
                public void onResponse(Call<OrderResponse> call, Response<OrderResponse> response) {
                    Log.d("ORDER_DEBUG", "Response code: " + response.code());
                    
                    if (response.isSuccessful() && response.body() != null) {
                        OrderResponse orderResponse = response.body();
                        Log.d("ORDER_DEBUG", "Response success: " + orderResponse.isSuccess());
                        Log.d("ORDER_DEBUG", "Response message: " + orderResponse.getMessage());
                        
                        if (orderResponse.isSuccess()) {
                            // Xóa giỏ hàng
                            CartManager.getInstance().clearCart();
                            
                            // Hiển thị thông báo thành công
                            Toast.makeText(CheckoutActivity.this, 
                                "Đặt hàng thành công! Cảm ơn bạn đã mua hàng.", 
                                Toast.LENGTH_LONG).show();
                            
                            // Quay về màn hình chính
                            Intent intent = new Intent(CheckoutActivity.this, ProductMainActivity.class);
                            intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
                            startActivity(intent);
                            finish();
                        } else {
                            Toast.makeText(CheckoutActivity.this, 
                                "Đặt hàng thất bại: " + orderResponse.getMessage(), 
                                Toast.LENGTH_SHORT).show();
                        }
                    } else {
                        try {
                            String errorBody = response.errorBody().string();
                            Log.e("ORDER_ERROR", "Error response code: " + response.code());
                            Log.e("ORDER_ERROR", "Error body: " + errorBody);
                            Toast.makeText(CheckoutActivity.this, 
                                "Lỗi server: " + errorBody, 
                                Toast.LENGTH_SHORT).show();
                        } catch (Exception e) {
                            Log.e("ORDER_ERROR", "Error parsing error body: " + e.getMessage());
                            Toast.makeText(CheckoutActivity.this, 
                                "Lỗi kết nối server", 
                                Toast.LENGTH_SHORT).show();
                        }
                    }
                }

                @Override
                public void onFailure(Call<OrderResponse> call, Throwable t) {
                    Log.e("ORDER_ERROR", "Network error: " + t.getMessage());
                    Log.e("ORDER_ERROR", "Stack trace: ", t);
                    Toast.makeText(CheckoutActivity.this, 
                        "Lỗi kết nối: " + t.getMessage(), 
                        Toast.LENGTH_SHORT).show();
                }
            });
        } catch (Exception e) {
            Log.e("ORDER_ERROR", "Error processing order: " + e.getMessage());
            Log.e("ORDER_ERROR", "Stack trace: ", e);
            Toast.makeText(this, "Lỗi xử lý đơn hàng: " + e.getMessage(), Toast.LENGTH_SHORT).show();
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == VNPAY_PAYMENT_REQUEST) {
            if (resultCode == RESULT_OK) {
                Toast.makeText(this, "Thanh toán thành công!", Toast.LENGTH_LONG).show();
                // Xử lý khi thanh toán thành công
            } else {
                Toast.makeText(this, "Thanh toán không thành công hoặc bị hủy", Toast.LENGTH_LONG).show();
                // Xử lý khi thanh toán thất bại hoặc bị hủy
            }
        }
    }
} 