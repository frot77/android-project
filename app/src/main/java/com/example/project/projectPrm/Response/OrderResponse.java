package com.example.project.projectPrm.Response;

import com.google.gson.annotations.SerializedName;

public class OrderResponse {
    @SerializedName("success")
    private int success;

    @SerializedName("message")
    private String message;

    public boolean isSuccess() {
        return success == 1;
    }

    public String getMessage() {
        return message;
    }
} 