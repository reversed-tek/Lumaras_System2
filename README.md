
An isolated RESTful API backend service handling shift slot assignments, real-time locking mechanisms, and employee state management.

---

## 📌 Architecture & Ecosystem

This microservice runs in a separate PHP-FPM container and does not render direct UI pages. It exposes HTTP JSON endpoints consumed directly by **[Lumaras_System1 (Main CRUD App)](https://github.com/reversed-tek/Lumaras_System1)** via internal proxy calls.

* **Main System Repo:** [Lumaras_System1](https://github.com/reversed-tek/Lumaras_System1)
* **Docker Setup Repo:** [Lumaras_DockerCodeBase](https://github.com/reversed-tek/Lumaras_DockerCodeBase)

---

## 📡 Key API Endpoints

| Endpoint | Method | Description |
| :--- | :--- | :--- |
| `/api.php` | `GET` | Fetches available shift slots (supports `?include_slot_id=X`) |
| `/get_employees.php` | `GET` | Fetches employee directory (supports `?all=1` for inactive records) |
| `/mark_slot_taken.php` | `POST` | Locks a shift slot and updates status to taken |
| `/release_slot.php` | `POST` | Releases a locked shift slot back to available status |
| `/add_employee.php` | `POST` | Adds a new employee record to the microservice database |

---

## 🔧 Local Development

This service is intended to run inside the Docker container stack managed by [Lumaras_DockerCodeBase](https://github.com/reversed-tek/Lumaras_DockerCodeBase).
