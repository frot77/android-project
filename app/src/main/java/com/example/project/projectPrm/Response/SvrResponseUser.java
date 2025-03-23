package com.example.project.projectPrm.Response;

import com.google.gson.annotations.SerializedName;

public class SvrResponseUser {
    private String success;
    private String message;
    @SerializedName("user")
    private User user;

    public static class User {
        private String id;
        @SerializedName("full_name")
        private String fullName;

        public String getId() {
            return id;
        }

        public void setId(String id) {
            this.id = id;
        }

        public String getFullName() {
            return fullName;
        }

        public void setFullName(String fullName) {
            this.fullName = fullName;
        }
    }

    public String getSuccess() {
        return success;
    }

    public void setSuccess(String success) {
        this.success = success;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public User getUser() {
        return user;
    }

    public void setUser(User user) {
        this.user = user;
    }
}
