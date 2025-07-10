-- Add notes column to scholarship_applications table
-- Run this query in phpMyAdmin to add the missing column

ALTER TABLE scholarship_applications 
ADD COLUMN notes TEXT AFTER application_date; 