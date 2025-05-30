# Your Next Show (YNS)

**Your Next Show** is a cost-free, ad-free platform designed to empower artists, venues, promoters, and creatives to connect and collaborate. Whether you're a band looking for a gig, a promoter searching for talent, or a photographer offering services, YNS makes it easy to find and be found.

## 🚀 What is YNS?

YNS helps artists and collaborators:
- Find venues, promoters, designers, photographers, and more in any area
- Create detailed profiles to showcase availability, services, and past work
- Access customized dashboards tailored to your role
- Manage events, finances, ticket sales, and jobs
- Browse gig listings or post opportunities

## 🔥 Key Features

- **Role-Based Dashboards**: Custom interfaces for artists, venues, and promoters
- **Opportunity Listings**: Post and find gig opportunities
- **Gig Guide**: Discover local shows or search by location
- **Rich Profile System**: Comprehensive profiles with all essential information
- **Data-Driven Structure**: Clear, organized information architecture

## 🛠 Tech Stack

- **Backend**: Laravel 11
- **Frontend**: TailwindCSS
- **Database**: MySQL
- **Development**: Docker

## 🧑‍💻 Installation

1. **Clone the repository**
```bash
git clone https://github.com/your-org/your-next-show.git
cd your-next-show
```

2. **Copy environment file**
```bash
cp .env.example .env
```

3. **Start Docker containers**
```bash
./vendor/bin/sail up -d
```

4. **Install dependencies**
```bash
./vendor/bin/sail composer install
npm install
```

5. **Set up database**
```bash
./vendor/bin/sail artisan migrate --seed
```

6. **Compile assets**
```bash
npm run build
npm run dev
```

## 🧪 Testing

Run the test suite:
```bash
./vendor/bin/sail artisan test
```

## 📝 License

[MIT License](LICENSE.md)

## 🤝 Contributing

Please read our [Contributing Guidelines](CONTRIBUTING.md) before submitting a pull request.