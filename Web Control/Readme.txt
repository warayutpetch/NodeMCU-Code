ไฟล์ admin.php เป็นหน้าหลักใช้ความคุมการทำงาน
ไฟล์ config.php ใช้ตั้งค่า host เพื่อส่งข้อมูลไปที่ database ของ host นั้น ๆ
ไฟล์ connect.php ใช้เชื่อต่อ database กับ host ที่เลือกฝากไฟล์
ไฟล์ index.php หน้า login ของระบบ username admin password 12345678
ไฟล์ login_ck.php เช็ค login ว่ารหัสถูกต้องหรือไม่
ไฟล์ logout.php ใช้สำหรับ kill session ทำให้ไม่ login ค้างไว้
ไฟล์ select.php ติดต่อกับ host เพื่อเรียกใช้ข้อมูลใน database เรียกความชึ้นมาแสดงในไฟล์ admin.php
ไฟล์ temp.php ใช้ get medthod เพื่อรอรับค่าความชื้นจาก NodeMCU และส่งขึ้นไปที่ database ของ host นั้นๆ
ไฟล์ table.sql ใช้ import database เพื่อเก็บค่าความชื้นไว้ใน database
***ถ้าต้องการใช้งานต้องมี host เพื่อฝากไฟล์หน้าเว็บเพราะเขียนด้วยภาษา php ไม่สามารถรันบนเครื่อง pc ได้หาก ไม่มี server ให้ใช้โปรแกรม xampp เพื่อจำลองเซิฟเวอร์