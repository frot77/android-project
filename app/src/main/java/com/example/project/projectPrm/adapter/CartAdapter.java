package com.example.project.projectPrm.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;

import com.example.project.R;
import com.example.project.projectPrm.CartActivity;
import com.example.project.projectPrm.CartManager;
import com.example.project.projectPrm.Response.Product;
import com.squareup.picasso.Picasso;

import java.text.NumberFormat;
import java.util.List;
import java.util.Locale;

public class CartAdapter extends ArrayAdapter<Product> {
    private Context context;
    private TextView tvTotalPrice;

    public CartAdapter(Context context, List<Product> objects, TextView tvTotalPrice) {
        super(context, 0, objects);
        this.context = context;
        this.tvTotalPrice = tvTotalPrice;
    }

    @NonNull
    @Override
    public View getView(int position, @Nullable View convertView, @NonNull ViewGroup parent) {
        if (convertView == null) {
            convertView = LayoutInflater.from(parent.getContext()).inflate(R.layout.cart_item, parent, false);
        }

        View listItem = convertView;

        // Lấy sản phẩm hiện tại
        Product product = getItem(position);
        int quantity = CartManager.getInstance().getCartItems().get(product);

        ImageView img = listItem.findViewById(R.id.cartitem_ivProduct);
        Picasso.get().load(product.getImage_url()).into(img);
        
        // Hiển thị thông tin sản phẩm
        TextView productName = listItem.findViewById(R.id.cartitem_tvName);
        productName.setText(product.getName());

        TextView productQuantity = listItem.findViewById(R.id.cartitem_tvQuantity);
        productQuantity.setText(String.valueOf(quantity));

        // Hiển thị giá sản phẩm
        TextView productPrice = listItem.findViewById(R.id.cartitem_tvPrice);
        double price = Double.parseDouble(product.getPrice()) * quantity;
        String formattedPrice = NumberFormat.getNumberInstance(Locale.US).format(price) + " VND";
        productPrice.setText(formattedPrice);

        // Xử lý sự kiện tăng số lượng
        ImageButton btnIncrease = listItem.findViewById(R.id.cartitem_btnIncrease);
        btnIncrease.setOnClickListener(v -> {
            CartManager.getInstance().addProductToCart(product);
            notifyDataSetChanged();
            updateTotalPrice();
        });

        // Xử lý sự kiện giảm số lượng
        ImageButton btnDecrease = listItem.findViewById(R.id.cartitem_btnDecrease);
        btnDecrease.setOnClickListener(v -> {
            if (quantity > 1) {
                CartManager.getInstance().decreaseProductQuantity(product);
                notifyDataSetChanged();
                updateTotalPrice();
            } else {
                // Nếu số lượng là 1, xóa sản phẩm khỏi giỏ hàng
                CartManager.getInstance().removeProductFromCart(product);
                remove(product);
                notifyDataSetChanged();
                updateTotalPrice();
                Toast.makeText(context, "Đã xóa sản phẩm khỏi giỏ hàng", Toast.LENGTH_SHORT).show();
                
                // Nếu giỏ hàng trống, quay lại màn hình trước
                if (getCount() == 0) {
                    ((CartActivity) context).finish();
                }
            }
        });

        // Xử lý sự kiện xóa sản phẩm
        ImageButton btnDelete = listItem.findViewById(R.id.cartitem_btnDelete);
        btnDelete.setOnClickListener(v -> {
            CartManager.getInstance().removeProductFromCart(product);
            remove(product);
            notifyDataSetChanged();
            updateTotalPrice();
            Toast.makeText(context, "Đã xóa sản phẩm khỏi giỏ hàng", Toast.LENGTH_SHORT).show();
            
            // Nếu giỏ hàng trống, quay lại màn hình trước
            if (getCount() == 0) {
                ((CartActivity) context).finish();
            }
        });

        // Cập nhật tổng giá trị
        updateTotalPrice();

        return listItem;
    }

    private void updateTotalPrice() {
        double total = 0;
        for (Product product : CartManager.getInstance().getCartItems().keySet()) {
            int quantity = CartManager.getInstance().getCartItems().get(product);
            total += Double.parseDouble(product.getPrice()) * quantity;
        }
        String formattedTotal = NumberFormat.getNumberInstance(Locale.US).format(total) + " VND";
        tvTotalPrice.setText("Tổng tiền: " + formattedTotal);
    }
}
