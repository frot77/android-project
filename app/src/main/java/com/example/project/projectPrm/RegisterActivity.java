package com.example.project.projectPrm;

import android.os.Bundle;
import android.text.TextUtils;
import android.util.Patterns;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.project.R;
import com.example.project.projectPrm.Response.SvrResponseUser;
import com.example.project.projectPrm.api.InterfaceLogin;
import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class RegisterActivity extends AppCompatActivity {
    private EditText etUsername, etPassword, etEmail, etFullname;
    private Button btnRegister;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);

        // Khởi tạo views
        etUsername = findViewById(R.id.et_username);
        etPassword = findViewById(R.id.et_password);
        etEmail = findViewById(R.id.et_email);
        etFullname = findViewById(R.id.et_fullname);
        btnRegister = findViewById(R.id.btn_register);

        // Xử lý sự kiện đăng ký
        btnRegister.setOnClickListener(v -> {
            String username = etUsername.getText().toString().trim();
            String password = etPassword.getText().toString().trim();
            String email = etEmail.getText().toString().trim();
            String fullname = etFullname.getText().toString().trim();

            // Kiểm tra dữ liệu đầu vào
            if (TextUtils.isEmpty(username)) {
                etUsername.setError("Vui lòng nhập tên đăng nhập");
                return;
            }
            if (TextUtils.isEmpty(password)) {
                etPassword.setError("Vui lòng nhập mật khẩu");
                return;
            }
            if (TextUtils.isEmpty(email)) {
                etEmail.setError("Vui lòng nhập email");
                return;
            }
            if (!Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
                etEmail.setError("Email không hợp lệ");
                return;
            }
            if (TextUtils.isEmpty(fullname)) {
                etFullname.setError("Vui lòng nhập họ tên");
                return;
            }

            // Gọi API đăng ký
            register(username, password, email, fullname);
        });
    }

    private void register(String username, String password, String email, String fullname) {
        // Tạo Gson với cấu hình lenient
        Gson gson = new GsonBuilder()
                .setLenient()
                .create();

        // Tạo Retrofit
        Retrofit retrofit = new Retrofit.Builder()
                .baseUrl("http://10.33.54.186/apiuser/")
                .addConverterFactory(GsonConverterFactory.create(gson))
                .build();

        // Tạo interface
        InterfaceLogin ilogin = retrofit.create(InterfaceLogin.class);

        // Gọi API
        Call<SvrResponseUser> call = ilogin.register(username, password, email, fullname);
        call.enqueue(new Callback<SvrResponseUser>() {
            @Override
            public void onResponse(Call<SvrResponseUser> call, Response<SvrResponseUser> response) {
                if (response.isSuccessful() && response.body() != null) {
                    SvrResponseUser svrResponseUser = response.body();
                    String success = svrResponseUser.getSuccess();

                    if ("1".equals(success)) {
                        // Đăng ký thành công
                        Toast.makeText(RegisterActivity.this, "Đăng ký thành công", Toast.LENGTH_SHORT).show();
                        finish();
                    } else {
                        // Đăng ký thất bại
                        Toast.makeText(RegisterActivity.this, svrResponseUser.getMessage(), Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Toast.makeText(RegisterActivity.this, "Lỗi kết nối server", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<SvrResponseUser> call, Throwable t) {
                Toast.makeText(RegisterActivity.this, "Lỗi kết nối: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }
} 