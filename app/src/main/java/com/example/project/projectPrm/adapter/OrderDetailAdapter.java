package com.example.project.projectPrm.adapter;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.example.project.R;
import com.example.project.projectPrm.Response.OrderHistoryResponse;
import com.squareup.picasso.Picasso;

import java.text.NumberFormat;
import java.util.List;
import java.util.Locale;

public class OrderDetailAdapter extends RecyclerView.Adapter<OrderDetailAdapter.OrderItemViewHolder> {
    private List<OrderHistoryResponse.OrderItem> items;

    public OrderDetailAdapter(List<OrderHistoryResponse.OrderItem> items) {
        this.items = items;
    }

    @NonNull
    @Override
    public OrderItemViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_order_detail, parent, false);
        return new OrderItemViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull OrderItemViewHolder holder, int position) {
        OrderHistoryResponse.OrderItem item = items.get(position);
        holder.bind(item);
    }

    @Override
    public int getItemCount() {
        return items != null ? items.size() : 0;
    }

    static class OrderItemViewHolder extends RecyclerView.ViewHolder {
        private ImageView ivProduct;
        private TextView tvProductName;
        private TextView tvPrice;
        private TextView tvQuantity;
        private TextView tvSubtotal;

        public OrderItemViewHolder(@NonNull View itemView) {
            super(itemView);
            ivProduct = itemView.findViewById(R.id.ivProduct);
            tvProductName = itemView.findViewById(R.id.tvProductName);
            tvPrice = itemView.findViewById(R.id.tvPrice);
            tvQuantity = itemView.findViewById(R.id.tvQuantity);
            tvSubtotal = itemView.findViewById(R.id.tvSubtotal);
        }

        public void bind(OrderHistoryResponse.OrderItem item) {
            tvProductName.setText(item.getProductName());
            tvQuantity.setText(item.getQuantity());

            // Format giá sản phẩm
            try {
                double price = Double.parseDouble(item.getPrice());
                String formattedPrice = NumberFormat.getNumberInstance(Locale.US)
                        .format(price) + " VND";
                tvPrice.setText(formattedPrice);

                // Tính và format thành tiền
                int quantity = Integer.parseInt(item.getQuantity());
                double subtotal = price * quantity;
                String formattedSubtotal = NumberFormat.getNumberInstance(Locale.US)
                        .format(subtotal) + " VND";
                tvSubtotal.setText("Thành tiền: " + formattedSubtotal);
            } catch (NumberFormatException e) {
                tvPrice.setText("0 VND");
                tvSubtotal.setText("Thành tiền: 0 VND");
            }

            // Load ảnh sản phẩm
            String imageUrl = item.getImageUrl();
            if (imageUrl != null && !imageUrl.isEmpty()) {
                Picasso.get()
                        .load(imageUrl)
                        .fit()
                        .centerCrop()
                        .placeholder(R.drawable.placeholder_image)
                        .error(R.drawable.placeholder_image)
                        .into(ivProduct);
            } else {
                ivProduct.setImageResource(R.drawable.placeholder_image);
            }
        }
    }
} 