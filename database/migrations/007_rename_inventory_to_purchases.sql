-- Migration 007: Split "inventory" expense category into separate concerns.
-- "purchases" = P&L expense (cost of buying goods for resale).
-- "Inventories" on the Balance Sheet becomes a manual-only field (no auto-calc from expenses).

UPDATE `expenses`
SET `category` = 'purchases'
WHERE `category` = 'inventory';
