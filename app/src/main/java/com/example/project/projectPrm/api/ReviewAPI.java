package com.example.project.projectPrm.api;

import com.example.project.projectPrm.Response.ReviewResponse;

import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.Query;

public interface ReviewAPI {
    @FormUrlEncoded
    @POST("apiuser/create_review.php")
    Call<ReviewResponse> createReview(
        @Field("user_id") String userId,
        @Field("product_id") String productId,
        @Field("rating") float rating,
        @Field("comment") String comment
    );

    @GET("apiuser/check_purchase_status.php")
    Call<ReviewResponse> checkPurchaseStatus(
        @Query("user_id") String userId,
        @Query("product_id") String productId
    );

    @GET("apiuser/check_has_reviewed.php")
    Call<ReviewResponse> checkHasReviewed(
        @Query("user_id") String userId,
        @Query("product_id") String productId
    );
} 