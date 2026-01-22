# 🎲 Coisas Inúteis

![Coisas Inúteis](assets/coisasInuteis.png)

Uma aplicação PHP divertida que fornece conselhos aleatórios, fatos curiosos e informações sobre números - basicamente, um repositório de coisas inúteis e interessantes!

## 🚀 Recursos

- **Conselhos Aleatórios**: Obtenha dicas criativas e inúteis
- **Fatos Curiosos**: Descubra fatos interessantes e surpreendentes
- **Números Mágicos**: Explore propriedades curiosas de números
- **API RESTful**: Acesse os dados via endpoints HTTP
- **Rate Limiting**: Proteção contra abuso com middleware de limite de requisições
- **Docker**: Pronto para containerização

## � Começar Rápido

### Localmente

```bash
# Instalar dependências
composer install

# Executar testes
composer test

# Verificar sintaxe PHP
composer lint

# Iniciar servidor de desenvolvimento
php -S 0.0.0.0:8000 -t public
```

### Com Docker

```bash
docker build -f docker/Dockerfile -t coisas-inuteis .
docker run -p 8000:8080 coisas-inuteis
```

## �📋 Pré-requisitos

- PHP 8.2+
- Composer
- Docker (opcional)

## 🔧 Instalação

1. Clone o repositório:
```bash
git clone <repository-url>
cd Coisas_Inuteis
```

2. Instale as dependências:
```bash
composer install
```

3. Configure o arquivo `.env`:
```bash
cp .env.example .env
```

## 🐳 Usando Docker

```bash
docker build -f docker/Dockerfile -t coisas-inuteis .
docker run -p 8000:80 coisas-inuteis
```

## 📁 Estrutura do Projeto

```
app/
├── Controllers/        # Controladores da aplicação
├── Data/              # Dados JSON (conselhos, fatos, números)
├── Middleware/        # Middlewares (Rate Limiting)
└── Services/          # Lógica de negócio

public/
├── index.php          # Arquivo de entrada principal
└── router.php         # Roteador customizado

tests/                 # Testes unitários
```

## 🧪 Testes

Execute os testes com PHPUnit:

```bash
./vendor/bin/phpunit
```

## 📝 Uso da API

### Endpoint Raiz
```
GET /
```
Retorna informação sobre os endpoints disponíveis.

### Obter um conselho aleatório
```
GET /conselho
```
Resposta: `{"conselho": "Nunca confie em um pato com chapéu."}`

### Obter um fato curioso
```
GET /fato
```
Resposta: `{"fato": "O primeiro fax enviado continha uma piada ruim..."}`

### Obter um número aleatório
```
GET /numero
```
Resposta: `{"numero": 42}`

### Contribuir com novo item
```
POST /contribuir
Content-Type: application/json

{
  "type": "conselho",
  "value": "Seu novo conselho inútil"
}
```
Tipos aceitos: `conselho`, `fato`, `numero`

#### Rate Limiting
O endpoint `POST /contribuir` possui limite de requisições:
- **Máximo**: 10 requisições por minuto (por IP)
- **Header de resposta**: `Retry-After` (segundos até próxima tentativa)

## 📚 Dependências Principais

- **slim/slim**: Framework PHP minimalista
- **nikic/fast-route**: Roteador rápido
- **phpunit/phpunit**: Framework de testes

## 📄 Licença

Este projeto está licenciado sob a [LICENSE](LICENSE).

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se livre para abrir issues e pull requests.

---

**Desenvolvido com ❤️ e muita inutilidade criativa** 🎉