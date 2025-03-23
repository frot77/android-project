package com.example.project.projectPrm.api;

import com.example.project.projectPrm.Response.SvrResponseUser;

import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;

public interface InterfaceLogin {
    @FormUrlEncoded
    @POST("login.php")
    Call<SvrResponseUser> login(
        @Field("username") String username,
        @Field("password") String password
    );

    @FormUrlEncoded
    @POST("signup.php")
    Call<SvrResponseUser> register(
        @Field("username") String username,
        @Field("password") String password,
        @Field("email") String email,
        @Field("full_name") String fullName
    );


}
