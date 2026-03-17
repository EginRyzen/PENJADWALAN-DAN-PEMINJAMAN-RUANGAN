-- Manual SQL Update for pengajuan_ruangans table
-- Run this script to update the schema if the table already exists.

-- 1. Add the new column
ALTER TABLE pengajuan_ruangans ADD COLUMN dokumen_pendukung_id CHAR(36) NULL;

-- 2. Add the foreign key constraint
ALTER TABLE pengajuan_ruangans ADD CONSTRAINT pengajuan_ruangans_dokumen_pendukung_id_foreign 
FOREIGN KEY (dokumen_pendukung_id) REFERENCES data_documents(id);

-- 3. (Optional) Drop the old column if no longer needed
-- ALTER TABLE pengajuan_ruangans DROP COLUMN dokumen_pendukung;
