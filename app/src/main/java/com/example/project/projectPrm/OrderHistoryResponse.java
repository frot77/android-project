package com.example.project.projectPrm;

import android.os.Parcel;
import android.os.Parcelable;
import com.google.gson.annotations.SerializedName;
import java.util.List;

public class OrderHistoryResponse {
    @SerializedName("success")
    private int success;

    @SerializedName("message")
    private String message;

    @SerializedName("orders")
    private List<Order> orders;

    public boolean isSuccess() {
        return success == 1;
    }

    public String getMessage() {
        return message;
    }

    public List<Order> getOrders() {
        return orders;
    }

    public static class Order implements Parcelable {
        @SerializedName("order_id")
        private String orderId;

        @SerializedName("created_at")
        private String orderDate;

        @SerializedName("recipient_name")
        private String recipientName;

        @SerializedName("recipient_phone")
        private String recipientPhone;

        @SerializedName("recipient_address")
        private String recipientAddress;

        @SerializedName("payment_method")
        private String paymentMethod;

        @SerializedName("total_price")
        private String totalAmount;

        @SerializedName("status")
        private String status;

        @SerializedName("items")
        private List<OrderItem> items;

        public String getOrderId() { return orderId; }
        public String getOrderDate() { return orderDate; }
        public String getRecipientName() { return recipientName; }
        public String getRecipientPhone() { return recipientPhone; }
        public String getRecipientAddress() { return recipientAddress; }
        public String getPaymentMethod() { return paymentMethod; }
        public String getTotalAmount() { return totalAmount; }
        public String getStatus() { return status; }
        public List<OrderItem> getItems() { return items; }

        // Parcelable implementation
        protected Order(Parcel in) {
            orderId = in.readString();
            orderDate = in.readString();
            recipientName = in.readString();
            recipientPhone = in.readString();
            recipientAddress = in.readString();
            paymentMethod = in.readString();
            totalAmount = in.readString();
            status = in.readString();
            items = in.createTypedArrayList(OrderItem.CREATOR);
        }

        @Override
        public void writeToParcel(Parcel dest, int flags) {
            dest.writeString(orderId);
            dest.writeString(orderDate);
            dest.writeString(recipientName);
            dest.writeString(recipientPhone);
            dest.writeString(recipientAddress);
            dest.writeString(paymentMethod);
            dest.writeString(totalAmount);
            dest.writeString(status);
            dest.writeTypedList(items);
        }

        @Override
        public int describeContents() {
            return 0;
        }

        public static final Creator<Order> CREATOR = new Creator<Order>() {
            @Override
            public Order createFromParcel(Parcel in) {
                return new Order(in);
            }

            @Override
            public Order[] newArray(int size) {
                return new Order[size];
            }
        };
    }

    public static class OrderItem implements Parcelable {
        @SerializedName("product_id")
        private String productId;

        @SerializedName("product_name")
        private String productName;

        @SerializedName("quantity")
        private String quantity;

        @SerializedName("price")
        private String price;

        @SerializedName("image_url")
        private String imageUrl;

        public String getProductId() { return productId; }
        public String getProductName() { return productName; }
        public String getQuantity() { return quantity; }
        public String getPrice() { return price; }
        public String getImageUrl() { return imageUrl; }

        // Parcelable implementation
        protected OrderItem(Parcel in) {
            productId = in.readString();
            productName = in.readString();
            quantity = in.readString();
            price = in.readString();
            imageUrl = in.readString();
        }

        @Override
        public void writeToParcel(Parcel dest, int flags) {
            dest.writeString(productId);
            dest.writeString(productName);
            dest.writeString(quantity);
            dest.writeString(price);
            dest.writeString(imageUrl);
        }

        @Override
        public int describeContents() {
            return 0;
        }

        public static final Creator<OrderItem> CREATOR = new Creator<OrderItem>() {
            @Override
            public OrderItem createFromParcel(Parcel in) {
                return new OrderItem(in);
            }

            @Override
            public OrderItem[] newArray(int size) {
                return new OrderItem[size];
            }
        };
    }
} 