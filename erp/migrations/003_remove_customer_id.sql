-- Remove tenant-specific customer_id from ERP tables for DRN single-tenant setup

-- erp_companies
ALTER TABLE erp_companies 
  DROP INDEX idx_erp_companies_customer_id;
ALTER TABLE erp_companies 
  DROP COLUMN customer_id;

-- erp_employees
ALTER TABLE erp_employees 
  DROP INDEX idx_erp_employees_customer_id;
ALTER TABLE erp_employees 
  DROP COLUMN customer_id;

-- erp_payrolls
ALTER TABLE erp_payrolls 
  DROP INDEX idx_erp_payrolls_customer_id;
ALTER TABLE erp_payrolls 
  DROP COLUMN customer_id;

-- erp_shifts
ALTER TABLE erp_shifts 
  DROP FOREIGN KEY fk_shifts_customer_uniq;
ALTER TABLE erp_shifts 
  DROP INDEX idx_shift_customer;
ALTER TABLE erp_shifts 
  DROP COLUMN customer_id;

-- erp_stock
ALTER TABLE erp_stock 
  DROP FOREIGN KEY fk_stock_customer;
ALTER TABLE erp_stock 
  DROP COLUMN customer_id;

-- erp_inventory
ALTER TABLE erp_inventory 
  DROP FOREIGN KEY fk_inventory_customer;
ALTER TABLE erp_inventory 
  DROP COLUMN customer_id;

-- erp_sales
ALTER TABLE erp_sales 
  DROP FOREIGN KEY fk_sales_customer_uniq;
ALTER TABLE erp_sales 
  DROP INDEX idx_sales_customer;
ALTER TABLE erp_sales 
  DROP COLUMN customer_id;

-- Optional: realtime events schema cleanup if present (ignore if table not present)
-- NOTE: Some deployments may not have this table yet. Use DROP TABLE IF EXISTS instead of schema changes.
DROP TABLE IF EXISTS rt_events;


