package com.example.project.projectPrm.Response;

import com.google.gson.annotations.SerializedName;

public class Comment {
    @SerializedName("id")
    private String id;

    @SerializedName("product_id")
    private String productId;

    @SerializedName("user_id")
    private String userId;

    @SerializedName("username")
    private String username;

    @SerializedName("comment")
    private String comment;

    @SerializedName("rating")
    private String rating;

    @SerializedName("created_at")
    private String createdAt;

    public String getId() {
        return id;
    }

    public String getProductId() {
        return productId;
    }

    public String getUserId() {
        return userId;
    }

    public String getUsername() {
        return username;
    }

    public String getComment() {
        return comment;
    }

    public String getRating() {
        return rating;
    }

    public String getCreatedAt() {
        return createdAt;
    }
} 