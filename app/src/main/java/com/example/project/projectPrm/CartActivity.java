package com.example.project.projectPrm;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;

import com.example.project.R;
import com.example.project.projectPrm.Response.Product;
import com.example.project.projectPrm.adapter.CartAdapter;

import java.util.ArrayList;
import java.util.Map;

public class CartActivity extends AppCompatActivity {
    private static final int LOGIN_REQUEST_CODE = 100;
    private ListView listview;
    private CartAdapter adapter;
    private TextView tvTotalPrice;
    private Button btnCheckout;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_cart);

        // Khởi tạo các view
        listview = findViewById(R.id.cart_listview);
        tvTotalPrice = findViewById(R.id.tv_total_price);
        btnCheckout = findViewById(R.id.btn_checkout);

        // Khởi tạo giỏ hàng
        CartManager cart = CartManager.getInstance();
        Map<Product, Integer> cartItems = cart.getCartItems();

        // Kiểm tra giỏ hàng có trống không
        if (cartItems.isEmpty()) {
            Toast.makeText(this, "Giỏ hàng trống", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        // Khởi tạo adapter
        adapter = new CartAdapter(this, new ArrayList<>(cartItems.keySet()), tvTotalPrice);
        listview.setAdapter(adapter);

        // Xử lý sự kiện thanh toán
        btnCheckout.setOnClickListener(v -> {
            if (isLoggedIn()) {
                // Nếu đã đăng nhập, chuyển đến trang điền thông tin
                Intent checkoutIntent = new Intent(CartActivity.this, CheckoutActivity.class);
                String totalText = tvTotalPrice.getText().toString();
                double totalAmount = Double.parseDouble(totalText.replaceAll("[^\\d.]", ""));
                checkoutIntent.putExtra("total_amount", totalAmount);
                startActivity(checkoutIntent);
            } else {
                // Nếu chưa đăng nhập, chuyển đến trang đăng nhập
                Intent loginIntent = new Intent(CartActivity.this, LoginActivity.class);
                startActivityForResult(loginIntent, LOGIN_REQUEST_CODE);
            }
        });
    }

    private boolean isLoggedIn() {
        SharedPreferences prefs = getSharedPreferences("user_prefs", MODE_PRIVATE);
        return prefs.contains("user_id"); // Giả sử bạn lưu user_id khi đăng nhập thành công
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        
        if (requestCode == LOGIN_REQUEST_CODE && resultCode == RESULT_OK) {
            // Đăng nhập thành công, chuyển đến trang điền thông tin
            Intent checkoutIntent = new Intent(CartActivity.this, CheckoutActivity.class);
            String totalText = tvTotalPrice.getText().toString();
            double totalAmount = Double.parseDouble(totalText.replaceAll("[^\\d.]", ""));
            checkoutIntent.putExtra("total_amount", totalAmount);
            startActivity(checkoutIntent);
        }
    }
}