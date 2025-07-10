-- Add password reset columns to users table
ALTER TABLE `users` 
ADD COLUMN `reset_token` VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `reset_expires` DATETIME NULL DEFAULT NULL;

-- Add index for better performance on reset token lookups
ALTER TABLE `users` 
ADD INDEX `idx_reset_token` (`reset_token`),
ADD INDEX `idx_reset_expires` (`reset_expires`); 