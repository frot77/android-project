package com.example.project.projectPrm;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RatingBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.example.project.R;
import com.example.project.projectPrm.Response.Comment;
import com.example.project.projectPrm.Response.CommentResponse;
import com.example.project.projectPrm.Response.Product;
import com.example.project.projectPrm.Response.ReviewResponse;
import com.example.project.projectPrm.adapter.CommentAdapter;
import com.example.project.projectPrm.api.CommentAPI;
import com.example.project.projectPrm.api.ReviewAPI;

import com.squareup.picasso.Picasso;

import java.text.NumberFormat;
import java.util.ArrayList;
import java.util.List;
import java.util.Locale;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class ProductDetailActivity extends AppCompatActivity {

    private static final String TAG = "ProductDetailActivity";
    private static final String BASE_URL = "http://192.168.34.106/";
    
    ImageView image;
    TextView tvName,tvDesc,tvPrice,tvStock;
    Button btn;
    private CartManager cartManager;
    private RecyclerView rvComments;
    private CommentAdapter commentAdapter;
    private CommentAPI commentAPI;
    private ReviewAPI reviewAPI;
    private List<Comment> commentList = new ArrayList<>();
    private TextView tvAverageRating;
    private Button btnAddReview;
    private String currentUserId;
    private String currentProductId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_product_detail);
        
        initializeViews();
        setupRecyclerView();
        setupRetrofit();
        
        // Lấy user_id từ SharedPreferences với tên đúng
        currentUserId = getSharedPreferences("user_prefs", MODE_PRIVATE)
            .getString("user_id", null);
        Log.d(TAG, "Current userId from SharedPreferences: " + currentUserId);

        // Nếu không có trong SharedPreferences, thử lấy từ Intent
        if (currentUserId == null || currentUserId.isEmpty()) {
            currentUserId = getIntent().getStringExtra("user_id");
            Log.d(TAG, "Current userId from Intent: " + currentUserId);
            
            // Nếu có từ intent thì lưu vào SharedPreferences
            if (currentUserId != null && !currentUserId.isEmpty()) {
                getSharedPreferences("user_prefs", MODE_PRIVATE)
                    .edit()
                    .putString("user_id", currentUserId)
                    .apply();
            }
        }

        // Kiểm tra button và log trạng thái
        if (btnAddReview != null) {
            Log.d(TAG, "btnAddReview initialized successfully");
            // Mặc định ẩn button, chỉ hiện khi API trả về thành công
            btnAddReview.setVisibility(View.GONE);
        } else {
            Log.e(TAG, "btnAddReview is null after initializeViews");
        }
        
        //receive
        Intent intent = getIntent();
        Product product = intent.getParcelableExtra("PRODUCT");
        
        //hien thi thong tin
        if(product != null){
            currentProductId = product.getId();
            Log.d(TAG, "Product ID: " + currentProductId);
            displayProductInfo(product);
            loadComments(product.getId());
            checkCanReview(product.getId());
        }
        
        //add product to cart
        btn.setOnClickListener(v -> {
            if(product != null){
                cartManager.addProductToCart(product);
                //open new activity
                Intent cartIntent = new Intent(this, CartActivity.class);
                startActivity(cartIntent);
            }
        });

        //add review
        if (btnAddReview != null) {
            btnAddReview.setOnClickListener(v -> {
                Log.d(TAG, "Add review button clicked. Current userId: " + currentUserId);
                if (currentUserId != null) {
                    showAddReviewDialog();
                } else {
                    Toast.makeText(this, "Vui lòng đăng nhập để đánh giá sản phẩm", Toast.LENGTH_SHORT).show();
                    // Chuyển đến màn hình đăng nhập
                    Intent loginIntent = new Intent(this, LoginActivity.class);
                    startActivity(loginIntent);
                }
            });
        } else {
            Log.e(TAG, "btnAddReview is null when setting click listener");
        }
    }

    private void initializeViews() {
        cartManager = CartManager.getInstance();
        image = findViewById(R.id.product_Image);
        tvName = findViewById(R.id.product_Name);
        tvDesc = findViewById(R.id.product_Description);
        tvStock = findViewById(R.id.product_Stock);
        tvPrice = findViewById(R.id.product_Price);
        btn = findViewById(R.id.btn_AddToCart);
        rvComments = findViewById(R.id.rvComments);
        tvAverageRating = findViewById(R.id.tvAverageRating);
        btnAddReview = findViewById(R.id.btnAddReview);
        
        // Log trạng thái của btnAddReview
        if (btnAddReview == null) {
            Log.e(TAG, "Failed to find btnAddReview in layout");
        } else {
            Log.d(TAG, "Successfully found btnAddReview");
            // Mặc định ẩn button
            btnAddReview.setVisibility(View.GONE);
        }
    }

    private void setupRecyclerView() {
        commentAdapter = new CommentAdapter(commentList);
        rvComments.setLayoutManager(new LinearLayoutManager(this));
        rvComments.setAdapter(commentAdapter);
    }

    private void setupRetrofit() {
        Retrofit retrofit = new Retrofit.Builder()
                .baseUrl(BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
        commentAPI = retrofit.create(CommentAPI.class);
        reviewAPI = retrofit.create(ReviewAPI.class);
    }

    private void displayProductInfo(Product product) {
        Picasso.get().load(product.getImage_url()).into(image);
        tvName.setText(product.getName());
        tvDesc.setText(product.getDescription());
        String formattedTotal = NumberFormat.getNumberInstance(Locale.US).format(Double.parseDouble(product.getPrice())) + " VND";
        tvPrice.setText(formattedTotal);
        tvStock.setText(product.getStock());
    }

    private void checkCanReview(String productId) {
        if (currentUserId == null) {
            btnAddReview.setVisibility(View.GONE);
            return;
        }
        
        reviewAPI.checkPurchaseStatus(currentUserId, productId).enqueue(new Callback<ReviewResponse>() {
            @Override
            public void onResponse(Call<ReviewResponse> call, Response<ReviewResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    ReviewResponse purchaseResponse = response.body();
                    
                    runOnUiThread(() -> {
                        if (purchaseResponse.getSuccess() == 1) {
                            checkHasReviewed(productId);
                        } else {
                            btnAddReview.setVisibility(View.GONE);
                        }
                    });
                } else {
                    runOnUiThread(() -> {
                        btnAddReview.setVisibility(View.GONE);
                    });
                }
            }

            @Override
            public void onFailure(Call<ReviewResponse> call, Throwable t) {
                runOnUiThread(() -> {
                    btnAddReview.setVisibility(View.GONE);
                });
            }
        });
    }

    private void checkHasReviewed(String productId) {
        reviewAPI.checkHasReviewed(currentUserId, productId).enqueue(new Callback<ReviewResponse>() {
            @Override
            public void onResponse(Call<ReviewResponse> call, Response<ReviewResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    ReviewResponse reviewResponse = response.body();
                    
                    runOnUiThread(() -> {
                        if (reviewResponse.getHasReviewed()) {
                            btnAddReview.setVisibility(View.GONE);
                        } else {
                            btnAddReview.setVisibility(View.VISIBLE);
                            btnAddReview.setEnabled(true);
                        }
                    });
                } else {
                    runOnUiThread(() -> {
                        btnAddReview.setVisibility(View.GONE);
                    });
                }
            }

            @Override
            public void onFailure(Call<ReviewResponse> call, Throwable t) {
                runOnUiThread(() -> {
                    btnAddReview.setVisibility(View.GONE);
                });
            }
        });
    }

    private void showAddReviewDialog() {
        View dialogView = LayoutInflater.from(this).inflate(R.layout.dialog_add_review, null);
        RatingBar ratingBar = dialogView.findViewById(R.id.ratingBar);
        EditText etComment = dialogView.findViewById(R.id.etComment);

        AlertDialog.Builder builder = new AlertDialog.Builder(this)
                .setView(dialogView)
                .setTitle("Đánh giá sản phẩm")
                .setPositiveButton("Gửi", null)
                .setNegativeButton("Hủy", null);

        AlertDialog dialog = builder.create();
        dialog.show();

        dialog.getButton(AlertDialog.BUTTON_POSITIVE).setOnClickListener(v -> {
            float rating = ratingBar.getRating();
            String comment = etComment.getText().toString().trim();
            
            if (rating == 0 || comment.isEmpty()) {
                return;
            }

            createReview(rating, comment);
            dialog.dismiss();
        });
    }

    private void createReview(float rating, String comment) {
        reviewAPI.createReview(currentUserId, currentProductId, rating, comment)
                .enqueue(new Callback<ReviewResponse>() {
                    @Override
                    public void onResponse(Call<ReviewResponse> call, Response<ReviewResponse> response) {
                        if (response.isSuccessful() && response.body() != null) {
                            ReviewResponse reviewResponse = response.body();
                            if (reviewResponse.getSuccess() == 1) {
                                Toast.makeText(ProductDetailActivity.this, 
                                    "Đánh giá thành công!", Toast.LENGTH_SHORT).show();
                                loadComments(currentProductId); // Reload comments
                                btnAddReview.setVisibility(View.GONE); // Ẩn nút sau khi đánh giá thành công
                            }
                        }
                    }

                    @Override
                    public void onFailure(Call<ReviewResponse> call, Throwable t) {
                        Log.e(TAG, "Create review failed", t);
                    }
                });
    }

    private void loadComments(String productId) {
        commentAPI.getProductComments(productId).enqueue(new Callback<CommentResponse>() {
            @Override
            public void onResponse(Call<CommentResponse> call, Response<CommentResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    CommentResponse commentResponse = response.body();
                    if (commentResponse.getSuccess() == 1 && commentResponse.getReviews() != null) {
                        commentList.clear();
                        commentList.addAll(commentResponse.getReviews());
                        commentAdapter.notifyDataSetChanged();
                        
                        // Hiển thị rating trung bình
                        if (commentResponse.getTotal_reviews() > 0) {
                            String ratingText = String.format("%.1f ★ (%d đánh giá)", 
                                commentResponse.getAverage_rating(), 
                                commentResponse.getTotal_reviews());
                            tvAverageRating.setText(ratingText);
                            tvAverageRating.setVisibility(View.VISIBLE);
                        } else {
                            tvAverageRating.setVisibility(View.GONE);
                        }
                    }
                }
            }

            @Override
            public void onFailure(Call<CommentResponse> call, Throwable t) {
                Log.e(TAG, "Load comments failed", t);
            }
        });
    }
}