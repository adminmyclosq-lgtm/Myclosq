GUT RESET — XAMPP LOCAL DEPLOYMENT

1. Extract the project to C:\xampp\htdocs\gutreset
2. Start Apache and MySQL in XAMPP.
3. Open a VS Code terminal in the project.
4. composer install
5. copy .env.xampp.example .env
6. php artisan key:generate
7. Import database/schema/phase1c_final_production_mysql.sql into MySQL 8.
8. php artisan db:seed
9. php artisan storage:link
10. npm install
11. npm run build
12. Configure Apache VirtualHost from docs/local/LOCAL_XAMPP_SETUP.md
13. Add 127.0.0.1 gutreset.local to the Windows hosts file.
14. Restart Apache.
15. Open http://gutreset.local/

Local demo admin: admin@gutreset.local / ChangeMe!123
Local demo customer: customer@gutreset.local / ChangeMe!123

External Razorpay, Meta WhatsApp and courier credentials are intentionally blank.
Local queue is synchronous; Redis is not required for UI/functional testing.
