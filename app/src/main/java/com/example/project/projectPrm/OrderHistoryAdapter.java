package com.example.project.projectPrm;

import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.example.project.R;

import java.text.NumberFormat;
import java.util.List;
import java.util.Locale;

public class OrderHistoryAdapter extends RecyclerView.Adapter<OrderHistoryAdapter.OrderViewHolder> {
    private List<OrderHistoryResponse.Order> orders;

    public OrderHistoryAdapter(List<OrderHistoryResponse.Order> orders) {
        this.orders = orders;
    }

    @NonNull
    @Override
    public OrderViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_order, parent, false);
        return new OrderViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull OrderViewHolder holder, int position) {
        OrderHistoryResponse.Order order = orders.get(position);
        holder.bind(order);
    }

    @Override
    public int getItemCount() {
        return orders != null ? orders.size() : 0;
    }

    static class OrderViewHolder extends RecyclerView.ViewHolder {
        private TextView tvOrderId;
        private TextView tvOrderDate;
        private TextView tvRecipientName;
        private TextView tvTotalAmount;
        private TextView tvStatus;

        public OrderViewHolder(@NonNull View itemView) {
            super(itemView);
            tvOrderId = itemView.findViewById(R.id.tvOrderId);
            tvOrderDate = itemView.findViewById(R.id.tvOrderDate);
            tvRecipientName = itemView.findViewById(R.id.tvRecipientName);
            tvTotalAmount = itemView.findViewById(R.id.tvTotalAmount);
            tvStatus = itemView.findViewById(R.id.tvStatus);
        }

        public void bind(OrderHistoryResponse.Order order) {
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
            
            tvRecipientName.setText(order.getRecipientName());
            
            // Format số tiền với kiểm tra null
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

            // Thêm sự kiện click
            itemView.setOnClickListener(v -> {
                Intent intent = new Intent(itemView.getContext(), OrderDetailActivity.class);
                intent.putExtra("order", order);
                itemView.getContext().startActivity(intent);
            });
        }
    }
} 