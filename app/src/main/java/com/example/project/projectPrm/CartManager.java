package com.example.project.projectPrm;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class CartManager {
    private static CartManager instance;
    private Map<Product, Integer> cartItems;

    private CartManager() {
        cartItems = new HashMap<>();
    }

    public static synchronized CartManager getInstance() {
        if (instance == null) {
            instance = new CartManager();
        }
        return instance;
    }

    public void addProductToCart(Product product) {
        if (product == null) return;
        
        if (cartItems.containsKey(product)) {
            cartItems.put(product, cartItems.get(product) + 1); // Tăng số lượng nếu sản phẩm đã có
        } else {
            cartItems.put(product, 1); // Thêm mới với số lượng 1
        }
    }

    public void decreaseProductQuantity(Product product) {
        if (product == null) return;
        
        if (cartItems.containsKey(product)) {
            int currentQuantity = cartItems.get(product);
            if (currentQuantity > 1) {
                cartItems.put(product, currentQuantity - 1);
            }
        }
    }

    public void removeProductFromCart(Product product) {
        if (product == null) return;
        cartItems.remove(product);
    }

    public Map<Product, Integer> getCartItems() {
        return cartItems;
    }

    public void clearCart() {
        cartItems.clear();
    }
}
