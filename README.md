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

## 📋 Pré-requisitos

- PHP 7.4+
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

### Obter um conselho aleatório
```
GET /advice
```

### Obter um fato curioso
```
GET /fact
```

### Obter informação de um número
```
GET /number/{id}
```

## 🛡️ Rate Limiting

A aplicação implementa um middleware de limite de requisições para proteção contra abuso. Configure os limites no arquivo `.env`.

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