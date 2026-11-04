-- Migration 011: loan tracking (Expenses → Pinjaman).
-- Applied automatically on first use by App\Core\Schema::ensureLoans(); kept here for fresh installs.
CREATE TABLE IF NOT EXISTS `loans` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`        INT UNSIGNED  NOT NULL,
    `lender`         VARCHAR(100)  NOT NULL,
    `principal`      DECIMAL(15,2) NOT NULL,
    `rate`           DECIMAL(6,3)  NOT NULL DEFAULT 0,
    `rate_type`      VARCHAR(10)   NOT NULL DEFAULT 'reducing',
    `months`         SMALLINT UNSIGNED NOT NULL,
    `installment`    DECIMAL(15,2) NOT NULL,
    `loan_date`      DATE          NOT NULL,
    `first_due_date` DATE          NOT NULL,
    `paid_before`    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `add_to_cash`    TINYINT(1)    NOT NULL DEFAULT 1,
    `created_at`     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_loans_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `loan_payments` (
    `id`                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `loan_id`              INT UNSIGNED  NOT NULL,
    `user_id`              INT UNSIGNED  NOT NULL,
    `pay_date`             DATE          NOT NULL,
    `amount`               DECIMAL(15,2) NOT NULL,
    `principal`            DECIMAL(15,2) NOT NULL,
    `interest`             DECIMAL(15,2) NOT NULL,
    `principal_expense_id` INT UNSIGNED  NULL,
    `interest_expense_id`  INT UNSIGNED  NULL,
    `created_at`           TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_lp_loan` (`loan_id`),
    KEY `idx_lp_user` (`user_id`),
    KEY `idx_lp_pexp` (`principal_expense_id`),
    KEY `idx_lp_iexp` (`interest_expense_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
