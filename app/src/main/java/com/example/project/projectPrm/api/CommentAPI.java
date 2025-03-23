package com.example.project.projectPrm.api;

import com.example.project.projectPrm.Response.CommentResponse;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Query;

public interface CommentAPI {
    @GET("apiuser/get_product_reviews.php")
    Call<CommentResponse> getProductComments(@Query("product_id") String productId);
} 