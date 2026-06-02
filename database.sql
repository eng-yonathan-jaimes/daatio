CREATE DATABASE daatio;

USE daatio;

CREATE TABLE IF NOT EXISTS user(
    id int AUTO_INCREMENT PRIMARY KEY,
    tenant_id int UNSIGNED NOT NULL,
    user_name varchar(255) NOT NULL,
    user_lastName varchar(255) NOT NULL,
    user_email varchar(255) NOT NULL,
    user_access varchar(255) NOT NULL,
    user_password char(60) NOT NULL,
    user_phone_number varchar(255) NOT NULL,
    user_update_date DATETIME NOT NULL,
    user_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_login_history(
    id int AUTO_INCREMENT PRIMARY KEY,
    user_login_history_login_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_login_history_ip varchar(15) NOT NULL,
    user_login_history_device varchar(255) NOT NULL,
    user_login_history_user_id int NOT NULL,
    FOREIGN KEY (user_login_history_user_id) REFERENCES user (id)
);

CREATE TABLE user_recovery_history(
    id int AUTO_INCREMENT PRIMARY KEY,
    user_recovery_history_intent_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_recovery_history_recovery_answered varchar(255) NOT NULL,
    user_recovery_history_recovered_success BOOLEAN NOT NULL,
    user_recovery_history_method_used varchar(255) NOT NULL,
    user_recovery_history_ip varchar(200) NOT NULL,
    user_recovery_history_user_id int NOT NULL,
    user_recovery_history_decive varchar(255) NOT NULL,
    FOREIGN KEY (user_recovery_history_user_id) REFERENCES user (id)
);

CREATE TABLE store_type(
    id int AUTO_INCREMENT PRIMARY KEY,
    store_type_description varchar(255) NOT NULL
);

CREATE TABLE store(
    id int AUTO_INCREMENT PRIMARY KEY,
    store_user_id int NOT NULL,
    store_name varchar(255) NOT NULL,
    store_active BOOLEAN NOT NULL,
    store_update_date DATETIME NOT NULL,
    store_address varchar(200) NOT NULL,
    store_type_id int NOT NULL,
    store_location ENUM('Physical','Online','Both') NOT NULL DEFAULT 'Physical',
    store_registration_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_user_id) REFERENCES user (id),
    FOREIGN KEY (store_type_id) REFERENCES store_type (id)
);

CREATE TABLE client(
    id int Not null auto_increment primary key,
    client_name varchar(255) Not null,
    client_last_name varchar(255) Not null,
    client_document_type ENUM('Cedula', 'Passport', 'PPT') Not null DEFAULT 'Cedula',
    client_document_number varchar(255) Not null,
    client_email varchar(255) Not null,
    client_phone_number varchar(255) Not null,
    client_registration_date Datetime Not null,
    client_update_date Datetime Not null,
    client_active Boolean Not null,
    client_type ENUM('Prompt', 'On-Term', 'Late', 'Partial', 'Deadbeat') Not null DEFAULT 'On-Term',
    client_stores_ids json Not null
);

CREATE TABLE client_state(
    id int Not null auto_increment primary key,
    client_state_client_id int Not null,
    client_state_store_id int Not null,
    client_state_amount DECIMAL(19,4) Not null, 
    client_state_state  enum('Favor', 'Debit', 'Settled') NOT NULL DEFAULT 'Settled',
    client_state_last_transaction_date Datetime Not null,
    FOREIGN KEY (client_state_client_id) REFERENCES client (id),
    FOREIGN KEY (client_state_store_id) REFERENCES store (id)
);

CREATE TABLE client_order(
    id int Not null auto_increment primary key,
    client_order_client_id int Not null,
    client_order_store_id int Not null,
    client_order_value decimal(19,4) Not null,
    client_order_state  enum('Favor', 'Debit', 'Settled') not null default 'Settled',
    client_order_registration_date Datetime Not null,
    client_order_date_due Datetime Not null,
    FOREIGN KEY (client_order_client_id) REFERENCES client (id),
    FOREIGN KEY (client_order_store_id) REFERENCES store (id)
);

CREATE TABLE transaction(
    id int Not null auto_increment primary key,
    transaction_client_id int Not null,
    transaction_store_id int Not null,
    transaction_client_order_id int Not null,
    transaction_transaction enum('Selling', 'Buying', 'Paying', 'Retriving') Not null,
    transaction_amount decimal(19,4) Not null,
    transaction_state enum('Favor', 'Debit', 'Settled') Not null,
    transaction_registration_date Datetime Not null,
    FOREIGN KEY (transaction_client_id) REFERENCES client (id),
    FOREIGN KEY (transaction_store_id) REFERENCES store (id),
    FOREIGN KEY (transaction_client_order_id) REFERENCES client_order (id)
);

CREATE TABLE product(
    id int Not null auto_increment primary key,
    product_store_id int Not null,
    product_name varchar(255) Not null,
    product_type int Not null,
    product_weight decimal(10,4) Not null,
    product_value decimal(19,4) Not null,
    product_state  enum('In Stock', 'Out of Stock') not null default 'Out of Stock',
    product_registration_date Datetime Not null,
    FOREIGN KEY (product_store_id) REFERENCES store (id)
);

CREATE TABLE client_list_order(
    id int Not null auto_increment primary key,
    client_list_order_client_id int Not null,
    client_list_order_store_id int Not null,
    client_list_order_id int Not null,
    client_list_order_product_id int Not null,
    client_list_order_weight decimal(10,4) Not null,
    client_list_order_registration_date Datetime Not null,
    client_list_order_value decimal(19,4) Null,
    FOREIGN KEY (client_list_order_client_id) REFERENCES client (id),
    FOREIGN KEY (client_list_order_store_id) REFERENCES store (id),
    FOREIGN KEY (client_list_order_id) REFERENCES client_order (id),
    FOREIGN KEY (client_list_order_product_id) REFERENCES product (id)
);