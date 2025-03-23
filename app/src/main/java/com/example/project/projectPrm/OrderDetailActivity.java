package com.example.project.projectPrm;

import android.os.Bundle;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.example.project.R;

import java.text.NumberFormat;
import java.util.Locale;

public class OrderDetailActivity extends AppCompatActivity {
    private TextView tvOrderId;
    private TextView tvOrderDate;
    private TextView tvStatus;
    private TextView tvRecipientName;
    private TextView tvRecipientPhone;
    private TextView tvRecipientAddress;
    private RecyclerView rvOrderItems;
    private TextView tvTotalAmount;
    private OrderDetailAdapter adapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_detail);

        // Khởi tạo views
        initViews();

        // Lấy thông tin đơn hàng từ Intent
        OrderHistoryResponse.Order order = getIntent().getParcelableExtra("order");
        if (order != null) {
            displayOrderDetails(order);
        }
    }

    private void initViews() {
        tvOrderId = findViewById(R.id.tvOrderId);
        tvOrderDate = findViewById(R.id.tvOrderDate);
        tvStatus = findViewById(R.id.tvStatus);
        tvRecipientName = findViewById(R.id.tvRecipientName);
        tvRecipientPhone = findViewById(R.id.tvRecipientPhone);
        tvRecipientAddress = findViewById(R.id.tvRecipientAddress);
        rvOrderItems = findViewById(R.id.rvOrderItems);
        tvTotalAmount = findViewById(R.id.tvTotalAmount);

        // Thiết lập RecyclerView
        rvOrderItems.setLayoutManager(new LinearLayoutManager(this));
    }

    private void displayOrderDetails(OrderHistoryResponse.Order order) {
        // Hiển thị thông tin đơn hàng
        tvOrderId.setText("DH" + order.getOrderId());
        
        // Format ngày đặt
        String orderDate = order.getOrderDate();
        if (orderDate != null && !orderDate.isEmpty()) {
            try {
                String[] dateTimeParts = orderDate.split(" ");
                String[] dateParts = dateTimeParts[0].split("-");
                String formattedDate = dateParts[2] + "/" + dateParts[1] + "/" + dateParts[0];
                tvOrderDate.setText(formattedDate);
            } catch (Exception e) {
                tvOrderDate.setText(orderDate);
            }
        } else {
            tvOrderDate.setText("N/A");
        }

        // Format trạng thái
        String status = order.getStatus();
        if (status != null) {
            switch (status.toLowerCase()) {
                case "pending":
                    tvStatus.setText("Chờ xử lý");
                    break;
                case "completed":
                    tvStatus.setText("Đã hoàn thành");
                    break;
                case "processing":
                    tvStatus.setText("Đang xử lý");
                    break;
                default:
                    tvStatus.setText(status);
            }
        } else {
            tvStatus.setText("N/A");
        }

        // Hiển thị thông tin người nhận
        tvRecipientName.setText(order.getRecipientName());
        tvRecipientPhone.setText(order.getRecipientPhone());
        tvRecipientAddress.setText(order.getRecipientAddress());

        // Hiển thị danh sách sản phẩm
        if (order.getItems() != null) {
            adapter = new OrderDetailAdapter(order.getItems());
            rvOrderItems.setAdapter(adapter);
        }

        // Format và hiển thị tổng tiền
        String totalAmount = order.getTotalAmount();
        if (totalAmount != null && !totalAmount.isEmpty()) {
            try {
                double amount = Double.parseDouble(totalAmount.trim());
                String formattedAmount = NumberFormat.getNumberInstance(Locale.US)
                        .format(amount) + " VND";
                tvTotalAmount.setText(formattedAmount);
            } catch (NumberFormatException e) {
                tvTotalAmount.setText("0 VND");
            }
        } else {
            tvTotalAmount.setText("0 VND");
        }
    }
} 