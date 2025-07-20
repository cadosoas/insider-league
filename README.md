# Insider Champions League

**Insider Champions League** is a mini football league simulation system built with **Laravel (Sail)** and **React**. It simulates matches, updates league standings, and calculates championship probabilities based on remaining fixtures.

## ⚽️ Features

- 4-team double round-robin league (6 matchweeks)
- Simulate all matches or one week at a time
- Live league table updates
- Reset simulation at any time
- Championship prediction starting from matchweek 4
- Clean Laravel architecture with Service, Repository, and Pipeline patterns
- Frontend built with React (Vite)

---

## 🚀 Installation

### 1. Clone the repository

```bash
git clone https://github.com/cadosoas/insider-league.git
cd insider-league
```

### 2. Start Laravel Sail


```bash
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```


### 3. Run the React frontend

```bash
cd frontend
npm install
npm run dev
```

---

## 🧪 Tests

```bash
./vendor/bin/sail artisan test
```
