package com.example.project.projectPrm.Response;

public class ReviewResponse {
    private int success;
    private String message;
    private boolean can_review;
    private boolean has_reviewed;

    public int getSuccess() {
        return success;
    }

    public void setSuccess(int success) {
        this.success = success;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public boolean getCanReview() {
        return can_review;
    }

    public void setCanReview(boolean can_review) {
        this.can_review = can_review;
    }

    public boolean getHasReviewed() {
        return has_reviewed;
    }

    public void setHasReviewed(boolean has_reviewed) {
        this.has_reviewed = has_reviewed;
    }
} 