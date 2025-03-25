package com.example.project.projectPrm;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.project.R;
import com.example.project.projectPrm.Response.Login;
import com.example.project.projectPrm.Response.SvrResponseUser;
import com.example.project.projectPrm.api.InterfaceLogin;
import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class LoginActivity extends AppCompatActivity {
    private EditText etUsername, etPassword;
    private Button btnLogin, btnGotoRegister;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        // Khởi tạo các view
        etUsername = findViewById(R.id.et_username);
        etPassword = findViewById(R.id.et_password);
        btnLogin = findViewById(R.id.btn_login);
        btnGotoRegister = findViewById(R.id.btn_goto_register);

        // Xử lý sự kiện đăng nhập
        btnLogin.setOnClickListener(v -> {
            String username = etUsername.getText().toString().trim();
            String password = etPassword.getText().toString().trim();

            // Kiểm tra dữ liệu đầu vào
            if (username.isEmpty()) {
                etUsername.setError("Vui lòng nhập tên đăng nhập");
                return;
            }
            if (password.isEmpty()) {
                etPassword.setError("Vui lòng nhập mật khẩu");
                return;
            }

            // Gọi phương thức đăng nhập
            login(username, password);
        });

        // Xử lý sự kiện chuyển sang màn hình đăng ký
        btnGotoRegister.setOnClickListener(v -> {
            Intent intent = new Intent(LoginActivity.this, RegisterActivity.class);
            startActivity(intent);
        });
    }

    private void login(String username, String password) {
        // Tạo đối tượng Login
        Login login = new Login();
        login.setUsername(username);
        login.setPassword(password);

        // Tạo Gson với cấu hình lenient
        Gson gson = new GsonBuilder()
                .setLenient()
                .create();

        // Tạo Retrofit
        Retrofit retrofit = new Retrofit.Builder()
                .baseUrl("http://192.168.34.106/apiuser/")
                .addConverterFactory(GsonConverterFactory.create(gson))
                .build();

        // Tạo interface
        InterfaceLogin ilogin = retrofit.create(InterfaceLogin.class);

        // Gọi API
        Call<SvrResponseUser> call = ilogin.login(username, password);
        call.enqueue(new Callback<SvrResponseUser>() {
            @Override
            public void onResponse(Call<SvrResponseUser> call, Response<SvrResponseUser> response) {
                if (response.isSuccessful() && response.body() != null) {
                    SvrResponseUser svrResponseUser = response.body();
                    String success = svrResponseUser.getSuccess();

                    if ("1".equals(success)) {
                        // Đăng nhập thành công
                        Toast.makeText(LoginActivity.this, "Đăng nhập thành công", Toast.LENGTH_SHORT).show();
                        
                        // Lưu thông tin đăng nhập
                        SharedPreferences prefs = getSharedPreferences("user_prefs", MODE_PRIVATE);
                        SharedPreferences.Editor editor = prefs.edit();
                        editor.putString("user_id", svrResponseUser.getUser().getId());
                        editor.putString("username", username);
                        editor.putString("full_name", svrResponseUser.getUser().getFullName());
                        editor.apply();
                        
                        // Trả về kết quả thành công
                        setResult(RESULT_OK);
                        finish();
                    } else {
                        // Đăng nhập thất bại
                        Toast.makeText(LoginActivity.this, svrResponseUser.getMessage(), Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Toast.makeText(LoginActivity.this, "Lỗi kết nối server", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<SvrResponseUser> call, Throwable t) {
                Toast.makeText(LoginActivity.this, "Lỗi kết nối: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }
} 