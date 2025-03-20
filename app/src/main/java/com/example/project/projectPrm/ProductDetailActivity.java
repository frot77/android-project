package com.example.project.projectPrm;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.example.project.R;
import com.squareup.picasso.Picasso;

import java.text.NumberFormat;
import java.util.Locale;

public class ProductDetailActivity extends AppCompatActivity {

    ImageView image;
    TextView tvName,tvDesc,tvPrice,tvStock;

    Button btn;

    private CartManager cartManager;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_product_detail);
        cartManager=CartManager.getInstance();
        image=findViewById(R.id.product_Image);
        tvName=findViewById(R.id.product_Name);
        tvDesc=findViewById(R.id.product_Description);
        tvStock=findViewById(R.id.product_Stock);
        tvPrice=findViewById(R.id.product_Price);
        btn=findViewById(R.id.btn_AddToCart);

        //receive
        Intent intent =getIntent();
        Product product=intent.getParcelableExtra("PRODUCT");
        //hien thi thong tin
        if(product!=null){
            Picasso.get().load(product.getImage_url()).into(image);
            tvName.setText(product.getName());
            tvDesc.setText(product.getDescription());
            String formattedTotal = NumberFormat.getNumberInstance(Locale.US).format(Double.parseDouble(product.getPrice()) ) + " VND";
            tvPrice.setText(formattedTotal);
            tvStock.setText(product.getStock());


        }
        //add product to cart
        btn.setOnClickListener(v->{
            Intent intent1=getIntent();
            Product product1=intent1.getParcelableExtra("PRODUCT");
            if(product1!=null){
                cartManager.addProductToCart(product);
                //open new activity
                Intent cartIntent=new Intent(this,CartActivity.class);
                startActivity(cartIntent);
            }
        });



    }
}