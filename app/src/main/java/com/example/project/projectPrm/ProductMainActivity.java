package com.example.project.projectPrm;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.ListView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.project.R;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;

public class ProductMainActivity extends AppCompatActivity {

    private ListView listView;
    List<Product> list=new ArrayList<>();
    ProductAdapter adapter;
    Context context=this;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_product_main);
        listView=findViewById(R.id.ProductListview);
        adapter=new ProductAdapter(list,context);
        listView.setAdapter(adapter);
        // lay du lieu tu server

//        new FetchProductTask().execute();

    }

//    private class FetchProductTask extends AsyncTask<Void,Void,String>{
//        // doc du lieu tu server
//        @Override
//        protected String doInBackground(Void... voids) {
//            StringBuilder response =new StringBuilder();//chua du lieu doc duoc
//
//            try {
//                //duong dan doc du lieu
//                URL url=new URL("http://192.168.34.106/api8/api1.php");
//                //ket noi
//                HttpURLConnection connection=(HttpURLConnection) url.openConnection();
//                //thiet lap phuong thuc doc du lieu
//                connection.setRequestMethod("GET");
//                //tao buffer
//                BufferedReader reader=new BufferedReader(new InputStreamReader(connection.getInputStream()));
//                //doc theo tung dong du lieu
//                String line="";
//                while((line=reader.readLine())!=null){
//                    response.append(line);
//                }
//
//                reader.close();
//
//            } catch (MalformedURLException e) {
//                throw new RuntimeException(e);
//            } catch (IOException e) {
//                throw new RuntimeException(e);
//            }
//            return response.toString();
//        }
//        //tra ket quave client
//        @Override
//        protected void onPostExecute(String s) {
//        // xu ly ket qua
//            if(s!=null && !s.isEmpty()){
//                try{
//                    //lay ve doi tuong json
//                    JSONObject json=new JSONObject(s);
//                    //lay ve mang product
//                    JSONArray jsonArray=json.getJSONArray("products");
//
//                    for (int i=0;i<jsonArray.length();i++){
//                        //lay doi tuong con
//                        JSONObject prdObject=jsonArray.getJSONObject(i);
//                        //lay cac truong
//                        String id=prdObject.getString("id");
//                        String name=prdObject.getString("name");
//                        String desc=prdObject.getString("description");
//                        String price=prdObject.getString("price");
//                        String img=prdObject.getString("image_url");
//
//                        Product product=new Product(id,name,desc,price,img);
//
//                        list.add(product);
//                    }
//                    adapter.notifyDataSetChanged();//cap nhat lai
//                } catch (JSONException e) {
//                    throw new RuntimeException(e);
//                }
//
//            }
//            else{
//                Toast.makeText(getApplicationContext(),"loi doc du lieu",Toast.LENGTH_LONG).show();
//            }
//        }
//    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.main_menu, menu);
        updateMenuItems(menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        
        if (id == R.id.action_cart) {
            // Chuyển đến màn hình giỏ hàng
            Intent cartIntent = new Intent(this, CartActivity.class);
            startActivity(cartIntent);
            return true;
        } else if (id == R.id.action_login) {
            if (!isLoggedIn()) {
                // Nếu chưa đăng nhập, mở màn hình đăng nhập
                Intent loginIntent = new Intent(this, LoginActivity.class);
                startActivityForResult(loginIntent, LOGIN_REQUEST_CODE);
            }
            return true;
        } else if (id == R.id.action_logout) {
            // Xử lý đăng xuất
            logout();
            return true;
        }
        
        return super.onOptionsItemSelected(item);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode,  Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == LOGIN_REQUEST_CODE && resultCode == RESULT_OK) {
            // Cập nhật lại menu sau khi đăng nhập thành công
            invalidateOptionsMenu();
        }
    }

    private boolean isLoggedIn() {
        SharedPreferences prefs = getSharedPreferences("user_prefs", MODE_PRIVATE);
        return prefs.contains("user_id");
    }

    private void logout() {
        // Xóa thông tin đăng nhập
        SharedPreferences prefs = getSharedPreferences("user_prefs", MODE_PRIVATE);
        SharedPreferences.Editor editor = prefs.edit();
        editor.clear();
        editor.apply();

        // Xóa giỏ hàng
        CartManager.getInstance().clearCart();

        // Cập nhật lại menu
        invalidateOptionsMenu();
        
        Toast.makeText(this, "Đã đăng xuất", Toast.LENGTH_SHORT).show();
    }

    private void updateMenuItems(Menu menu) {
        MenuItem accountItem = menu.findItem(R.id.action_account);
        MenuItem loginItem = menu.findItem(R.id.action_login);
        MenuItem logoutItem = menu.findItem(R.id.action_logout);

        if (isLoggedIn()) {
            // Nếu đã đăng nhập
            SharedPreferences prefs = getSharedPreferences("user_prefs", MODE_PRIVATE);
            String fullName = prefs.getString("full_name", "");
            accountItem.setTitle(fullName);
            loginItem.setVisible(false);
            logoutItem.setVisible(true);
        } else {
            // Nếu chưa đăng nhập
            accountItem.setTitle("Tài khoản");
            loginItem.setVisible(true);
            logoutItem.setVisible(false);
        }
    }

    private static final int LOGIN_REQUEST_CODE = 100;
}