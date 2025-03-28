﻿-- Drop tables if they exist
DROP TABLE IF EXISTS reviews, payment, orderdetail, orders, product, categories, role, user;

-- Table: role
CREATE TABLE role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

-- Table: user
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100),
    role_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES role(id) ON DELETE SET NULL
);

-- Table: categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT
);

-- Table: product
CREATE TABLE product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
	image_url VARCHAR(255) NOT NULL,
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Table: orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recipient_name VARCHAR(100) NOT NULL,
    recipient_phone VARCHAR(20) NOT NULL,
    recipient_address TEXT NOT NULL,
    status ENUM('Pending', 'Processing', 'Completed', 'Cancelled') NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

-- Table: orderdetail
CREATE TABLE orderdetail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
);

-- Table: payment
CREATE TABLE payment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method ENUM('VNPAY') NOT NULL,
    payment_status ENUM('Pending','Completed', 'Failed') NOT NULL,
    transaction_id VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Table: reviews
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
);

INSERT INTO categories (name, description) VALUES
('Nhẫn', 'Các loại nhẫn vàng, bạc, kim cương'),
('Vòng tay', 'Các loại vòng tay phong thủy, vàng, bạc'),
('Dây chuyền', 'Dây chuyền vàng, bạc, đính đá cao cấp'),
('Bông tai', 'Bông tai vàng, bạc, ngọc trai, kim cương');

INSERT INTO product (category_id, name, description, price, stock, image_url) VALUES
(1, 'Nhẫn kim cương', 'Nhẫn kim cương 18K sang trọng', 15000000, 10, 'https://bizweb.dktcdn.net/thumb/1024x1024/100/337/476/products/img-4759.jpg?v=1711080013537'),
(1, 'Nhẫn vàng 24K', 'Nhẫn vàng trơn 24K cao cấp', 12000000, 15, 'https://product.hstatic.net/200000061680/product/nhan_tron_tron_2c_7a7d913b5dd744d7b1b59d02ed06c383_master.png'),
(2, 'Vòng tay bạc', 'Vòng tay bạc 925 tinh tế', 5000000, 20, 'https://tleejewelry.vn/wp-content/uploads/2022/06/Lac-tay-nu-TLEE-vong-tay-bac-sao-kep-ca-tinh-LT0117.jpg'),
(2, 'Vòng tay phong thủy', 'Vòng tay đá phong thủy mang lại may mắn', 3000000, 25, 'https://images.squarespace-cdn.com/content/v1/5fc46ed50b6b03258f4c2bb4/1615367385427-GY2OR52JER11AMAT59GV/1.png'),
(3, 'Dây chuyền vàng trắng', 'Dây chuyền vàng trắng 14K đính đá', 8000000, 12, 'https://apj.vn/wp-content/uploads/2020/11/MTD0038-day-chuyen-vang-trang-10k.jpg'),
(3, 'Dây chuyền bạc', 'Dây chuyền bạc 925 cao cấp', 4500000, 18, 'https://apj.vn/wp-content/uploads/2020/11/MTD0038-day-chuyen-vang-trang-10k.jpg'),
(4, 'Bông tai ngọc trai', 'Bông tai ngọc trai tự nhiên sang trọng', 6000000, 10, 'https://cdn.pnj.io/images/detailed/124/gbxmxmw001776-bong-tai-vang-trang-10k-dinh-da-ecz-pnj-1.png'),
(4, 'Bông tai kim cương', 'Bông tai đính kim cương lấp lánh', 20000000, 8, 'https://cdn.pnj.io/images/detailed/124/gbxmxmw001776-bong-tai-vang-trang-10k-dinh-da-ecz-pnj-1.png');

INSERT INTO role (name) VALUES
('ADMIN'),
('USER');
