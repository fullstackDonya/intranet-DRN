SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS erp_shifts;
DROP TABLE IF EXISTS erp_payrolls;
DROP TABLE IF EXISTS erp_sales;
DROP TABLE IF EXISTS erp_stock;
DROP TABLE IF EXISTS erp_inventory;
DROP TABLE IF EXISTS erp_employees;
DROP TABLE IF EXISTS erp_companies;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE IF NOT EXISTS erp_companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  siret VARCHAR(20) NULL,
  address_line1 VARCHAR(255) NULL,
  address_line2 VARCHAR(255) NULL,
  postal_code VARCHAR(20) NULL,
  city VARCHAR(120) NULL,
  country VARCHAR(120) DEFAULT 'France',
  phone VARCHAR(50) NULL,
  email VARCHAR(180) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS erp_employees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NULL,
  first_name VARCHAR(120) NOT NULL,
  last_name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NULL,
  hire_date DATE NULL,
  base_salary DECIMAL(10,2) DEFAULT 0,
  job_title VARCHAR(180) NULL,
  department VARCHAR(180) NULL,
  contract_type ENUM('CDI','CDD','Freelance','Stage','Alternance') DEFAULT 'CDI',
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_emp_company FOREIGN KEY (company_id) REFERENCES erp_companies(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS erp_payrolls (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id INT NOT NULL,
  period CHAR(7) NOT NULL COMMENT 'YYYY-MM',
  gross_salary DECIMAL(10,2) NOT NULL,
  bonus DECIMAL(10,2) NOT NULL DEFAULT 0,
  overtime DECIMAL(10,2) NOT NULL DEFAULT 0,
  deductions DECIMAL(10,2) NOT NULL DEFAULT 0,
  employee_contrib DECIMAL(10,2) NOT NULL,
  employer_contrib DECIMAL(10,2) NOT NULL,
  net_pay DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_emp_period (employee_id, period),
  CONSTRAINT fk_payroll_employee FOREIGN KEY (employee_id) REFERENCES erp_employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS erp_shifts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id INT DEFAULT NULL,
  start_datetime DATETIME NOT NULL,
  end_datetime DATETIME NOT NULL,
  role VARCHAR(150) DEFAULT NULL,
  notes TEXT,
  company_id INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_shift_employee (employee_id),
  INDEX idx_shift_company (company_id),
  CONSTRAINT fk_shifts_company FOREIGN KEY (company_id) REFERENCES erp_companies(id) ON DELETE SET NULL,
  CONSTRAINT fk_shifts_employee FOREIGN KEY (employee_id) REFERENCES erp_employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

// ...existing code...
CREATE TABLE IF NOT EXISTS erp_products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(64) NULL,
  name VARCHAR(255) NOT NULL,
  description VARCHAR(1000) NULL,
  quantity INT NOT NULL DEFAULT 0,            -- stock disponible pour la vente
  purchase_price DECIMAL(10,2) NULL,
  sale_price DECIMAL(10,2) NOT NULL,
  rental_rate_per_day DECIMAL(10,2) NULL,    -- tarif de location journalier (si applicable)
  is_rental TINYINT(1) NOT NULL DEFAULT 0,   -- 1 si peut être loué
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS erp_rentals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NULL,                       -- ou company_id selon modèle client
  start_date DATETIME NOT NULL,
  end_date DATETIME NOT NULL,
  status ENUM('draft','active','completed','cancelled') DEFAULT 'draft',
  total_price DECIMAL(10,2) DEFAULT 0,
  deposit DECIMAL(10,2) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS erp_rental_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rental_id INT NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  daily_rate DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_rental_items_rental (rental_id),
  INDEX idx_rental_items_product (product_id),
  CONSTRAINT fk_rental_items_rental FOREIGN KEY (rental_id) REFERENCES erp_rentals(id) ON DELETE CASCADE,
  CONSTRAINT fk_rental_items_product FOREIGN KEY (product_id) REFERENCES erp_products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- variante : table ventes (référence maintenant erp_products)
CREATE TABLE IF NOT EXISTS erp_sales (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  employee_id INT NULL,
  quantity INT NOT NULL,
  sale_price DECIMAL(10,2) NOT NULL,
  sale_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_sales_product (product_id),
  INDEX idx_sales_employee (employee_id),
  CONSTRAINT fk_sales_product FOREIGN KEY (product_id) REFERENCES erp_products(id) ON DELETE CASCADE,
  CONSTRAINT fk_sales_employee FOREIGN KEY (employee_id) REFERENCES erp_employees(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;