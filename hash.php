<?php
echo "Bless: " . password_hash("admin123", PASSWORD_DEFAULT) . "<br>";
echo "manager: " . password_hash("manager123", PASSWORD_DEFAULT) . "<br>";
echo "caissier: " . password_hash("caissier123", PASSWORD_DEFAULT);