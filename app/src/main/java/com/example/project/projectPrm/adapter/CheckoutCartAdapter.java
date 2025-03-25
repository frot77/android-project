package com.example.project.projectPrm.adapter;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.example.project.R;
import com.example.project.projectPrm.Response.Product;
import com.squareup.picasso.Picasso;

import java.text.NumberFormat;
import java.util.ArrayList;
import java.util.List;
import java.util.Locale;
import java.util.Map;

public class CheckoutCartAdapter extends RecyclerView.Adapter<CheckoutCartAdapter.CartViewHolder> {
    private List<Product> products;
    private Map<Product, Integer> cartItems;

    public CheckoutCartAdapter(Map<Product, Integer> cartItems) {
        this.cartItems = cartItems;
        this.products = new ArrayList<>(cartItems.keySet());
    }

    @NonNull
    @Override
    public CartViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_checkout_cart, parent, false);
        return new CartViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull CartViewHolder holder, int position) {
        Product product = products.get(position);
        int quantity = cartItems.get(product);
        holder.bind(product, quantity);
    }

    @Override
    public int getItemCount() {
        return products.size();
    }

    static class CartViewHolder extends RecyclerView.ViewHolder {
        private ImageView ivProduct;
        private TextView tvProductName;
        private TextView tvQuantity;
        private TextView tvPrice;
        private TextView tvSubtotal;

        public CartViewHolder(@NonNull View itemView) {
            super(itemView);
            ivProduct = itemView.findViewById(R.id.ivProduct);
            tvProductName = itemView.findViewById(R.id.tvProductName);
            tvQuantity = itemView.findViewById(R.id.tvQuantity);
            tvPrice = itemView.findViewById(R.id.tvPrice);
            tvSubtotal = itemView.findViewById(R.id.tvSubtotal);
        }

        public void bind(Product product, int quantity) {
            tvProductName.setText(product.getName());
            tvQuantity.setText("x" + quantity);
            
            // Format giá
            try {
                double price = Double.parseDouble(product.getPrice());
                String formattedPrice = NumberFormat.getNumberInstance(Locale.US).format(price) + " VND";
                tvPrice.setText(formattedPrice);
                
                // Tính và hiển thị tổng tiền cho sản phẩm
                double subtotal = price * quantity;
                String formattedSubtotal = NumberFormat.getNumberInstance(Locale.US).format(subtotal) + " VND";
                tvSubtotal.setText(formattedSubtotal);
            } catch (NumberFormatException e) {
                tvPrice.setText("0 VND");
                tvSubtotal.setText("0 VND");
            }

            // Load ảnh sản phẩm
            String imageUrl = product.getImage_url();
            if (imageUrl != null && !imageUrl.isEmpty()) {
                Picasso.get()
                    .load(imageUrl)
                    .placeholder(R.drawable.placeholder_image)
                    .error(R.drawable.placeholder_image)
                    .fit()
                    .centerCrop()
                    .into(ivProduct);
            } else {
                ivProduct.setImageResource(R.drawable.placeholder_image);
            }
        }
    }
} 