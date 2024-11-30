<?php
// Kết nối đến MySQL server
$servername = "localhost";
$username = "root";
$password = "";

// Kết nối đến MySQL
$conn = new mysqli($servername, $username, $password);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Tạo cơ sở dữ liệu 'haravan' nếu chưa tồn tại
$sql_create_db = "CREATE DATABASE IF NOT EXISTS haravan";
if ($conn->query($sql_create_db) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Chọn cơ sở dữ liệu 'haravan'
$conn->select_db('haravan');

// Mảng chứa các câu lệnh SQL để tạo bảng
$tables = [
    // Table: Employee
    "CREATE TABLE Employee (
        Employee_ID INT AUTO_INCREMENT PRIMARY KEY,
        Name VARCHAR(255) NOT NULL,
        Email VARCHAR(255) NOT NULL UNIQUE,
        Phone VARCHAR(20),
        Position VARCHAR(100),
        Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,  
        Updated_At DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    // Table: Vendor
    "CREATE TABLE Vendor (
        Vendor_ID INT(11) AUTO_INCREMENT PRIMARY KEY,
        Name TEXT NOT NULL UNIQUE,
        Address TEXT,
        Phone TEXT,
        Email TEXT,
        Website TEXT,
        Created_At DATETIME,
        Updated_At DATETIME,
        Created_By INT(11),
        Updated_By INT(11),
        FOREIGN KEY (Created_By) REFERENCES Employee(Employee_ID),
        FOREIGN KEY (Updated_By) REFERENCES Employee(Employee_ID)
    )",

    // Table: Product_Type
    "CREATE TABLE Product_Type (
        Type_ID INT AUTO_INCREMENT PRIMARY KEY,
        Type_Name VARCHAR(255) NOT NULL UNIQUE,
        Description TEXT,
        Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
        Updated_At DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        Created_By INT(11),
        Updated_By INT(11),
        FOREIGN KEY (Created_By) REFERENCES Employee(Employee_ID),
        FOREIGN KEY (Updated_By) REFERENCES Employee(Employee_ID)
    )",

    // Table: Industry
    "CREATE TABLE Industry (
        Industry_ID INT AUTO_INCREMENT PRIMARY KEY,
        Industry_Name VARCHAR(255) NOT NULL UNIQUE,
        Description TEXT,
        Status ENUM('active', 'inactive') DEFAULT 'active',
        Parent_Industry_ID INT NULL,
        Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
        Updated_At DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        Created_By INT(11),
        Updated_By INT(11),
        FOREIGN KEY (Parent_Industry_ID) REFERENCES Industry(Industry_ID) ON DELETE SET NULL,
        FOREIGN KEY (Created_By) REFERENCES Employee(Employee_ID),
        FOREIGN KEY (Updated_By) REFERENCES Employee(Employee_ID)
    )",

    // Table: Product_Attributes
    "CREATE TABLE Product_Attributes (
        Attribute_ID INT AUTO_INCREMENT PRIMARY KEY,
        Attribute_Name VARCHAR(255) NOT NULL UNIQUE,
        Description TEXT,
        Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
        Updated_At DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        Created_By INT(11),
        Updated_By INT(11),
        FOREIGN KEY (Created_By) REFERENCES Employee(Employee_ID),
        FOREIGN KEY (Updated_By) REFERENCES Employee(Employee_ID)
    )",

    // Table: Product_Attribute_Values
    "CREATE TABLE Product_Attribute_Values (
        Value_ID INT AUTO_INCREMENT PRIMARY KEY,
        Attribute_ID INT NOT NULL,
        Value_Name VARCHAR(255) NOT NULL,
        Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
        Updated_At DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        Created_By INT(11),
        Updated_By INT(11),
        UNIQUE (Attribute_ID, Value_Name),
        FOREIGN KEY (Attribute_ID) REFERENCES Product_Attributes(Attribute_ID) ON DELETE CASCADE,
        FOREIGN KEY (Created_By) REFERENCES Employee(Employee_ID),
        FOREIGN KEY (Updated_By) REFERENCES Employee(Employee_ID)
    )",

    // Table: Products
    "CREATE TABLE Products (
        Product_ID INT(11) AUTO_INCREMENT PRIMARY KEY,
        Title TEXT,
        Handle TEXT,
        Vendor_ID INT(11),
        Type_ID INT(11),
        Industry_ID INT(11),
        Description TEXT,
        Quote TEXT,
        Price DECIMAL(10,2),
        Compare_At_Price DECIMAL(10,2),
        Cost_Price DECIMAL(10,2),
        SKU TEXT,
        Barcode TEXT,
        Is_Inventory_Managed BOOLEAN,
        Is_Lot_Managed BOOLEAN,
        Allow_Backorder BOOLEAN,
        Allow_Shipping BOOLEAN,
        Weight DECIMAL(10,2),
        Has_Multiple_Units BOOLEAN,
        Has_Variants BOOLEAN,
        Is_Promoted BOOLEAN,
        Meta_Title TEXT,
        Meta_Description TEXT,
        Created_At DATETIME,
        Updated_At DATETIME,
        Created_By INT(11),
        Updated_By INT(11),
        FOREIGN KEY (Vendor_ID) REFERENCES Vendor(Vendor_ID),
        FOREIGN KEY (Type_ID) REFERENCES Product_Type(Type_ID),
        FOREIGN KEY (Industry_ID) REFERENCES Industry(Industry_ID),
        FOREIGN KEY (Created_By) REFERENCES Employee(Employee_ID),
        FOREIGN KEY (Updated_By) REFERENCES Employee(Employee_ID)
    )",

    // Table: Images
    "CREATE TABLE Images (
        Image_ID INT(11) AUTO_INCREMENT PRIMARY KEY,
        Product_ID INT(11),
        URL TEXT NOT NULL UNIQUE,
        Description TEXT,
        Created_At DATETIME,
        Updated_At DATETIME,
        Created_By INT(11),
        Updated_By INT(11),
        FOREIGN KEY (Product_ID) REFERENCES Products(Product_ID),
        FOREIGN KEY (Created_By) REFERENCES Employee(Employee_ID),
        FOREIGN KEY (Updated_By) REFERENCES Employee(Employee_ID)
    )",

    // Table: Product_Image
    "CREATE TABLE Product_Image (
        Product_Image_ID INT(11) AUTO_INCREMENT PRIMARY KEY,
        Product_ID INT(11),
        Image_ID INT(11),
        Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
        Updated_At DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        Created_By INT(11),
        Updated_By INT(11),
        FOREIGN KEY (Product_ID) REFERENCES Products(Product_ID),
        FOREIGN KEY (Image_ID) REFERENCES Images(Image_ID),
        FOREIGN KEY (Created_By) REFERENCES Employee(Employee_ID),
        FOREIGN KEY (Updated_By) REFERENCES Employee(Employee_ID)
    )",

    // Table: Product_Attribute_Assignments
    "CREATE TABLE Product_Attribute_Assignments (
        Assignment_ID INT AUTO_INCREMENT PRIMARY KEY,
        Product_ID INT NOT NULL,
        Value_ID INT NOT NULL,
        Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
        Updated_At DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        Created_By INT(11),
        Updated_By INT(11),
        UNIQUE (Product_ID, Value_ID),
        FOREIGN KEY (Product_ID) REFERENCES Products(Product_ID) ON DELETE CASCADE,
        FOREIGN KEY (Value_ID) REFERENCES Product_Attribute_Values(Value_ID) ON DELETE CASCADE,
        FOREIGN KEY (Created_By) REFERENCES Employee(Employee_ID),
        FOREIGN KEY (Updated_By) REFERENCES Employee(Employee_ID)
    )",

    // Table: Tag_Product
    "CREATE TABLE Tag_Product (
        ID INT(11) AUTO_INCREMENT PRIMARY KEY,
        Name TEXT,
        Created_At DATETIME,
        Updated_At DATETIME
    )",

    // Table: Product_Group
    "CREATE TABLE Product_Group (
        group_id INT(11) AUTO_INCREMENT PRIMARY KEY,
        group_name TEXT,
        description TEXT,
        Image_ID INT(11),
        Meta_Title TEXT,
        Meta_Description TEXT,
        Created_At DATETIME,
        Updated_At DATETIME,
        Created_By INT(11),
        Updated_By INT(11),
        FOREIGN KEY (Image_ID) REFERENCES Images(Image_ID),
        FOREIGN KEY (Created_By) REFERENCES Employee(Employee_ID),
        FOREIGN KEY (Updated_By) REFERENCES Employee(Employee_ID)
    )",

    // Table: Product_Group_Conditions
    "CREATE TABLE Product_Group_Conditions (
        ConditionID INT AUTO_INCREMENT PRIMARY KEY,
        Product_Group_ID INT NOT NULL,
        Source_Type ENUM('manual', 'auto') DEFAULT 'manual',
        Product_Attribute ENUM('Tên sản phẩm', 'Loại sản phẩm', 'Nhà cung cấp', 'Ngành hàng', 'Trọng lượng'),
        Comparison TEXT,
        Match_Type ENUM('all', 'any') DEFAULT 'any',
        FOREIGN KEY (Product_Group_ID) REFERENCES Product_Group(group_id)
    )",

    // Table: Price_List
    "CREATE TABLE Price_List (
        Id INT AUTO_INCREMENT PRIMARY KEY,
        Price_List_Name VARCHAR(255) NOT NULL,
        Description TEXT,
        Start_Time DATETIME NOT NULL,
        End_Time DATETIME NOT NULL,
        Status VARCHAR(50) NOT NULL DEFAULT 'Active',
        Created_At DATETIME DEFAULT CURRENT_TIMESTAMP,
        Created_By INT(11),
        FOREIGN KEY (Created_By) REFERENCES Employee(Employee_ID)
    )",

    // Table: Price_List_Products
    "CREATE TABLE Price_List_Products (
        Price_List_Id INT NOT NULL,
        Product_ID INT NOT NULL,
        PRIMARY KEY (Price_List_Id, Product_ID),
        FOREIGN KEY (Price_List_Id) REFERENCES Price_List(Id) ON DELETE CASCADE,
        FOREIGN KEY (Product_ID) REFERENCES Products(Product_ID) ON DELETE CASCADE
    )",

    // Table: Price_List_History
    "CREATE TABLE Price_List_History (
        Id INT AUTO_INCREMENT PRIMARY KEY,
        Price_List_Id INT NOT NULL,
        Change_Details TEXT NOT NULL,
        Performed_By INT NOT NULL,
        Performed_At DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (Price_List_Id) REFERENCES Price_List(Id),
        FOREIGN KEY (Performed_By) REFERENCES Employee(Employee_ID)
    )",

    // Table: Price_List_Conditions
    "CREATE TABLE Price_List_Conditions (
        Condition_Id INT AUTO_INCREMENT PRIMARY KEY,
        Price_List_Id INT NOT NULL,
        Channel ENUM('POS', 'Harasocial', 'All') DEFAULT 'All',
        Condition_Text TEXT NOT NULL,  -- Đổi tên cột
        FOREIGN KEY (Price_List_Id) REFERENCES Price_List(Id) ON DELETE CASCADE
    )",

    // Table: Branch
    "CREATE TABLE Branch (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        Name TEXT NOT NULL UNIQUE,
        Address TEXT,
        Phone TEXT UNIQUE,
        Email TEXT UNIQUE,
        Manager_ID INT,
        Created_At DATETIME,
        Updated_At DATETIME,
        Created_By INT(11),
        Updated_By INT(11),
        FOREIGN KEY (Created_By) REFERENCES Employee(Employee_ID),
        FOREIGN KEY (Updated_By) REFERENCES Employee(Employee_ID),
        FOREIGN KEY (Manager_ID) REFERENCES Employee(Employee_ID)
    )",

    // Table: Price_List_Condition_Branch
    "CREATE TABLE Price_List_Condition_Branch (
        Condition_Id INT NOT NULL,
        Branch_ID INT NOT NULL,
        PRIMARY KEY (Condition_Id, Branch_ID),
        FOREIGN KEY (Condition_Id) REFERENCES Price_List_Conditions(Condition_Id) ON DELETE CASCADE,
        FOREIGN KEY (Branch_ID) REFERENCES Branch(ID) ON DELETE CASCADE
    )"
];

// Thực thi các câu lệnh SQL
foreach ($tables as $table) {
    if ($conn->query($table) === TRUE) {
        echo "Table created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

// Đóng kết nối
$conn->close();
?>
