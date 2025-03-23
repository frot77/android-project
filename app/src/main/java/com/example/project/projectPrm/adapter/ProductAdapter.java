package com.example.project.projectPrm.adapter;

import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.example.project.R;
import com.example.project.projectPrm.Response.Product;
import com.example.project.projectPrm.ProductDetailActivity;
import com.squareup.picasso.Picasso;

import java.text.NumberFormat;
import java.util.List;
import java.util.Locale;

public class ProductAdapter extends BaseAdapter {
    private List<Product> list;

    private Context context;

    public ProductAdapter(List<Product> list, Context context) {
        this.list = list;
        this.context = context;
    }

    @Override
    public int getCount() {
        return list.size();
    }

    @Override
    public Object getItem(int position) {
        return list.get(position);
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        ProductViewHolder holder;
        if(convertView==null){
            // tao view blank
        convertView=LayoutInflater.from(context).inflate(R.layout.product_itemview,parent,false);

        // anh xa tung phan
            holder=new ProductViewHolder();
            holder.img=convertView.findViewById(R.id.img_product);
            holder.tvname=convertView.findViewById(R.id.tv_product_name);
            holder.tvdescription=convertView.findViewById(R.id.tv_product_description);
            holder.tvprice=convertView.findViewById(R.id.tv_product_price);

            // tao template
            convertView.setTag(holder);
        }
        else{
            holder=(ProductViewHolder) convertView.getTag(); //laytemplate ra dung
        }
        //set du lieu
        Product p=list.get(position);
        if(p!=null){
            holder.tvname.setText(p.getName());
            holder.tvdescription.setText(p.getDescription());
            String formattedPrice = NumberFormat.getNumberInstance(Locale.US).format(Double.parseDouble(p.getPrice()) ) + " VND";

            holder.tvprice.setText(formattedPrice);
            Picasso.get().load(p.getImage_url()).into(holder.img);
        }

        //event
        convertView.setOnClickListener(v->{
            Product product= list.get(position);
            Intent intent=new Intent(context, ProductDetailActivity.class);
            intent.putExtra("PRODUCT",product);
            context.startActivity(intent);
        });

        return convertView;
    }

    static class ProductViewHolder{
        ImageView img;

        TextView tvname,tvdescription,tvprice;
    }
}
