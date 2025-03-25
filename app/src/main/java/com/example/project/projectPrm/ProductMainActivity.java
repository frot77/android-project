package com.example.project.projectPrm;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.ListView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.project.R;
import com.example.project.projectPrm.Response.Product;
import com.example.project.projectPrm.adapter.ProductAdapter;
import com.google.android.material.tabs.TabLayout;

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
    private TabLayout tabLayout;
    private List<Product> allProducts = new ArrayList<>();
    private List<Product> filteredProducts = new ArrayList<>();
    private ProductAdapter adapter;
    private Context context = this;
    private String currentCategoryId = "0"; // 0 for all products

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_product_main);
        
        listView = findViewById(R.id.ProductListview);
        tabLayout = findViewById(R.id.tabLayout);
        
        adapter = new ProductAdapter(filteredProducts, context);
        listView.setAdapter(adapter);

        // Xử lý sự kiện khi chọn tab
        tabLayout.addOnTabSelectedListener(new TabLayout.OnTabSelectedListener() {
            @Override
            public void onTabSelected(TabLayout.Tab tab) {
                currentCategoryId = String.valueOf(tab.getPosition()); // "0" for all, "1"-"4" for categories
                filterProducts();
            }

            @Override
            public void onTabUnselected(TabLayout.Tab tab) {}

            @Override
            public void onTabReselected(TabLayout.Tab tab) {}
        });

        // Lấy dữ liệu từ server
        new FetchProductTask().execute();
    }

    private void filterProducts() {
        filteredProducts.clear();
        if (currentCategoryId.equals("0")) {
            // Hiển thị tất cả sản phẩm
            filteredProducts.addAll(allProducts);
            Log.d("Filter", "Showing all products: " + filteredProducts.size());
        } else {
            // Lọc theo category_id
            for (Product product : allProducts) {
                if (currentCategoryId.equals(product.getCategory_id())) {
                    filteredProducts.add(product);
                }
            }
            Log.d("Filter", "Showing category " + currentCategoryId + " products: " + filteredProducts.size());
        }
        adapter.notifyDataSetChanged();
    }

    private class FetchProductTask extends AsyncTask<Void, Void, String> {
        @Override
        protected String doInBackground(Void... voids) {
            StringBuilder response = new StringBuilder();
            try {
                URL url = new URL("http://10.33.54.186/api8/api1.php");
                HttpURLConnection connection = (HttpURLConnection) url.openConnection();
                connection.setRequestMethod("GET");
                
                int responseCode = connection.getResponseCode();
                Log.d("API", "Response code: " + responseCode);
                
                BufferedReader reader = new BufferedReader(new InputStreamReader(connection.getInputStream()));
                String line;
                while ((line = reader.readLine()) != null) {
                    response.append(line);
                }
                reader.close();
                
                Log.d("API", "Response: " + response.toString());
            } catch (Exception e) {
                Log.e("API", "Error fetching data", e);
                e.printStackTrace();
            }
            return response.toString();
        }

        @Override
        protected void onPostExecute(String s) {
            Log.d("API", "onPostExecute response: " + s);
            
            if (s != null && !s.isEmpty()) {
                try {
                    JSONObject json = new JSONObject(s);
                    JSONArray jsonArray = json.getJSONArray("products");
                    Log.d("API", "Products array size: " + jsonArray.length());

                    allProducts.clear();
                    for (int i = 0; i < jsonArray.length(); i++) {
                        JSONObject prdObject = jsonArray.getJSONObject(i);
                        String id = prdObject.getString("id");
                        String name = prdObject.getString("name");
                        String desc = prdObject.getString("description");
                        String price = prdObject.getString("price");
                        String img = prdObject.getString("image_url");
                        String categoryId = prdObject.getString("category_id");

                        Product product = new Product(id, name, desc, price, img);
                        product.setCategory_id(categoryId);
                        allProducts.add(product);
                        
                        Log.d("API", "Added product: " + name + " with category: " + categoryId);
                    }
                    
                    Log.d("API", "Total products loaded: " + allProducts.size());
                    filterProducts(); // Áp dụng bộ lọc hiện tại
                } catch (JSONException e) {
                    Log.e("API", "JSON parsing error", e);
                    e.printStackTrace();
                    Toast.makeText(getApplicationContext(), "Lỗi xử lý dữ liệu", Toast.LENGTH_LONG).show();
                }
            } else {
                Log.e("API", "Empty or null response");
                Toast.makeText(getApplicationContext(), "Lỗi đọc dữ liệu", Toast.LENGTH_LONG).show();
            }
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.menu_main, menu);
        updateMenuItems(menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        
        if (id == R.id.menu_cart) {
            startActivity(new Intent(this, CartActivity.class));
            return true;
        }
        else if (id == R.id.menu_order_history) {
            // Kiểm tra đăng nhập trước khi mở lịch sử đơn hàng
            if (isLoggedIn()) {
                startActivity(new Intent(this, OrderHistoryActivity.class));
            } else {
                // Nếu chưa đăng nhập, mở màn hình đăng nhập
                Intent loginIntent = new Intent(this, LoginActivity.class);
                startActivityForResult(loginIntent, LOGIN_REQUEST_CODE);
            }
            return true;
        }
        else if (id == R.id.menu_login) {
            Intent loginIntent = new Intent(this, LoginActivity.class);
            startActivityForResult(loginIntent, LOGIN_REQUEST_CODE);
            return true;
        }
        else if (id == R.id.menu_logout) {
            logout();
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == LOGIN_REQUEST_CODE && resultCode == RESULT_OK) {
            // Cập nhật lại menu sau khi đăng nhập thành công
            invalidateOptionsMenu();
            // Nếu đang cố gắng xem lịch sử đơn hàng, mở màn hình lịch sử
//            if (isLoggedIn()) {
//                startActivity(new Intent(this, OrderHistoryActivity.class));
//            }
        }
    }

    private boolean isLoggedIn() {
        SharedPreferences prefs = getSharedPreferences("user_prefs", MODE_PRIVATE);
        return !prefs.getString("user_id", "").isEmpty();
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
        MenuItem loginItem = menu.findItem(R.id.menu_login);
        MenuItem logoutItem = menu.findItem(R.id.menu_logout);
        MenuItem usernameItem = menu.findItem(R.id.menu_username);
        MenuItem userProfileItem = menu.findItem(R.id.menu_user_profile);

        SharedPreferences prefs = getSharedPreferences("user_prefs", MODE_PRIVATE);
        String username = prefs.getString("username", "");

        if (isLoggedIn()) {
            // Nếu đã đăng nhập
            loginItem.setVisible(false);
            logoutItem.setVisible(true);
            usernameItem.setTitle(username);
            userProfileItem.setTitle(username);
        } else {
            // Nếu chưa đăng nhập
            loginItem.setVisible(true);
            logoutItem.setVisible(false);
            usernameItem.setTitle("Chưa đăng nhập");
            userProfileItem.setTitle("Tài khoản");
        }
    }

    private static final int LOGIN_REQUEST_CODE = 100;
}