package com.example.project.projectPrm.api;

import com.example.project.projectPrm.Response.OrderHistoryResponse;
import com.example.project.projectPrm.Response.OrderResponse;

import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.Query;

public interface InterfaceOrder {
    @FormUrlEncoded
    @POST("apiuser/create_order.php")
    Call<OrderResponse> createOrder(
        @Field("user_id") String userId,
        @Field("recipient_name") String recipientName,
        @Field("recipient_phone") String recipientPhone,
        @Field("recipient_address") String recipientAddress,
        @Field("payment_method") String paymentMethod,
        @Field("items") String items
    );

    @GET("apiuser/get_user_orders.php")
    Call<OrderHistoryResponse> getUserOrders(
        @Query("user_id") String userId
    );
} 