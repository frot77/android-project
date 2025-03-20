package com.example.project.projectPrm;

import android.os.Bundle;
import android.text.TextUtils;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.project.R;

public class CheckoutActivity extends AppCompatActivity {
    private EditText etFullname, etPhone, etAddress;
    private TextView tvTotal;
    private Button btnConfirm;
    private double totalAmount;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_checkout);

        // Khởi tạo views
        etFullname = findViewById(R.id.et_fullname);
        etPhone = findViewById(R.id.et_phone);
        etAddress = findViewById(R.id.et_address);
        tvTotal = findViewById(R.id.tv_total);
        btnConfirm = findViewById(R.id.btn_confirm);

        // Lấy tổng tiền từ intent
        totalAmount = getIntent().getDoubleExtra("total_amount", 0);
        tvTotal.setText(String.format("Tổng tiền: %,.0fđ", totalAmount));

        // Xử lý sự kiện nút xác nhận
        btnConfirm.setOnClickListener(v -> validateAndSubmitOrder());
    }

    private void validateAndSubmitOrder() {
        // Lấy thông tin từ form
        String fullname = etFullname.getText().toString().trim();
        String phone = etPhone.getText().toString().trim();
        String address = etAddress.getText().toString().trim();

        // Kiểm tra thông tin
        if (TextUtils.isEmpty(fullname)) {
            etFullname.setError("Vui lòng nhập họ tên");
            return;
        }

        if (TextUtils.isEmpty(phone)) {
            etPhone.setError("Vui lòng nhập số điện thoại");
            return;
        }

        if (TextUtils.isEmpty(address)) {
            etAddress.setError("Vui lòng nhập địa chỉ");
            return;
        }

        // TODO: Gửi thông tin đơn hàng lên server
        // Tạm thời hiển thị thông báo thành công
        Toast.makeText(this, "Đặt hàng thành công!", Toast.LENGTH_SHORT).show();
        setResult(RESULT_OK);
        finish();
    }
} 