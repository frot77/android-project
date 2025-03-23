package com.example.project.projectPrm;

import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.example.project.R;
import com.example.project.projectPrm.Response.OrderHistoryResponse;
import com.example.project.projectPrm.adapter.OrderHistoryAdapter;
import com.example.project.projectPrm.api.InterfaceOrder;

import java.io.IOException;
import java.util.ArrayList;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class OrderHistoryActivity extends AppCompatActivity {
    private static final String TAG = "OrderHistoryActivity";
    private RecyclerView rvOrders;
    private OrderHistoryAdapter adapter;
    private ProgressBar progressBar;
    private TextView tvNoOrders;
    private static final String BASE_URL = "http://192.168.34.106/";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_history);

        // Khởi tạo views
        initViews();

        // Kiểm tra đăng nhập
        String userId = checkLogin();
        if (userId == null) return;

        // Tải dữ liệu
        loadOrderHistory(userId);
    }

    private void initViews() {
        rvOrders = findViewById(R.id.rvOrders);
        progressBar = findViewById(R.id.progressBar);
        tvNoOrders = findViewById(R.id.tvNoOrders);

        rvOrders.setLayoutManager(new LinearLayoutManager(this));
        adapter = new OrderHistoryAdapter(new ArrayList<>());
        rvOrders.setAdapter(adapter);
    }

    private String checkLogin() {
        String userId = getSharedPreferences("user_prefs", MODE_PRIVATE)
                .getString("user_id", "");

        if (userId.isEmpty()) {
            Toast.makeText(this, "Vui lòng đăng nhập để xem lịch sử đơn hàng", 
                Toast.LENGTH_SHORT).show();
            finish();
            return null;
        }
        return userId;
    }

    private void loadOrderHistory(String userId) {
        showLoading(true);
        Log.d(TAG, "Loading order history for userId: " + userId);

        try {
            // Khởi tạo Retrofit với logging
            Retrofit retrofit = new Retrofit.Builder()
                    .baseUrl(BASE_URL)
                    .addConverterFactory(GsonConverterFactory.create())
                    .build();

            InterfaceOrder interfaceOrder = retrofit.create(InterfaceOrder.class);

            // Gọi API
            Call<OrderHistoryResponse> call = interfaceOrder.getUserOrders(userId);
            call.enqueue(new Callback<OrderHistoryResponse>() {
                @Override
                public void onResponse(Call<OrderHistoryResponse> call, Response<OrderHistoryResponse> response) {
                    showLoading(false);
                    Log.d(TAG, "Response code: " + response.code());

                    if (response.isSuccessful() && response.body() != null) {
                        handleSuccessResponse(response.body());
                    } else {
                        handleErrorResponse(response);
                    }
                }

                @Override
                public void onFailure(Call<OrderHistoryResponse> call, Throwable t) {
                    showLoading(false);
                    handleFailure(t);
                }
            });
        } catch (Exception e) {
            showLoading(false);
            Log.e(TAG, "Error setting up network call", e);
            Toast.makeText(this, "Lỗi kết nối: " + e.getMessage(), Toast.LENGTH_SHORT).show();
        }
    }

    private void handleSuccessResponse(OrderHistoryResponse response) {
        Log.d(TAG, "Success: " + response.isSuccess() + ", Message: " + response.getMessage());
        
        if (response.isSuccess()) {
            if (response.getOrders() != null && !response.getOrders().isEmpty()) {
                adapter = new OrderHistoryAdapter(response.getOrders());
                rvOrders.setAdapter(adapter);
                showContent();
            } else {
                showNoOrders();
            }
        } else {
            Toast.makeText(this, response.getMessage(), Toast.LENGTH_SHORT).show();
            showNoOrders();
        }
    }

    private void handleErrorResponse(Response<OrderHistoryResponse> response) {
        try {
            String errorBody = response.errorBody() != null ? response.errorBody().string() : "Unknown error";
            Log.e(TAG, "Error response: " + errorBody);
            Toast.makeText(this, "Lỗi server: " + errorBody, Toast.LENGTH_SHORT).show();
        } catch (IOException e) {
            Log.e(TAG, "Error parsing error response", e);
            Toast.makeText(this, "Lỗi xử lý phản hồi từ server", Toast.LENGTH_SHORT).show();
        }
        showNoOrders();
    }

    private void handleFailure(Throwable t) {
        Log.e(TAG, "Network error", t);
        Toast.makeText(this, "Lỗi kết nối: " + t.getMessage(), Toast.LENGTH_SHORT).show();
        showNoOrders();
    }

    private void showLoading(boolean show) {
        progressBar.setVisibility(show ? View.VISIBLE : View.GONE);
        rvOrders.setVisibility(show ? View.GONE : View.VISIBLE);
        tvNoOrders.setVisibility(View.GONE);
    }

    private void showContent() {
        rvOrders.setVisibility(View.VISIBLE);
        progressBar.setVisibility(View.GONE);
        tvNoOrders.setVisibility(View.GONE);
    }

    private void showNoOrders() {
        rvOrders.setVisibility(View.GONE);
        progressBar.setVisibility(View.GONE);
        tvNoOrders.setVisibility(View.VISIBLE);
    }
} 