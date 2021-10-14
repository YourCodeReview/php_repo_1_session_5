# Базовая структура
---
- [app/Controllers/BaseController.php](app/Controllers/BaseController.php) - Базовый контроллер
- [app/Controllers/ApiController.php](app/Controllers/ApiController.php) - Основной контроллер API
- [app/Controllers/Api.php](app/Controllers/Api.php) - контроллер точки входа

Сущность User
- [app/Api/Auth.php](app/Api/Auth.php) - контроллер
- [app/Entities/User.php](app/Entities/User.php) - сущность
- [app/Models/UserModel.php](app/Models/UserModel.php) - модель

Аналогично для других сущностей:
- Device
  - [app/Api/Net/Dhcpsnoop.php](app/Api/Net/Dhcpsnoop.php) - контроллер
  - [app/Entities/Dhcp.php](app/Entities/Dhcp.php) - сущность
  - [app/Models/DhcpModel.php](app/Models/DhcpModel.php) - модель  
- Dhcpsnoop
  - [app/Api/Net/Device.php](app/Api/Net/Device.php) - контроллер
  - [app/Entities/NetDevice.php](app/Entities/NetDevice.php) - сущность
  - [app/Models/NetDeviceModel.php](app/Models/NetDeviceModel.php) - модель
