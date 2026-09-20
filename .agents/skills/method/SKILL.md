# Laravel Domain Architecture

## Purpose

This skill is used to develop Laravel applications using:

* Domain-Oriented Architecture
* Frontend and Backend Separation
* A single Laravel repository/project
* API as the boundary between FE and BE
* Business logic isolated from the HTTP layer
* Domain-oriented frontend structure
* Code that is easy to test, maintain, and scale

The agent MUST follow this skill when creating, modifying, or refactoring Laravel features.

---

# 1. Core Architecture

Use the following architecture:

```text
Frontend
    ↓
HTTP / API
    ↓
Controllers / Requests / Resources
    ↓
Domain
    ↓
Database / External Services
```

Frontend and Backend live in the same repository/project, but maintain a clear architectural boundary.

```text
project/
├── app/
│   ├── Domains/
│   ├── Http/
│   └── Support/
│
├── resources/
│   └── js/
│       ├── domains/
│       ├── components/
│       ├── layouts/
│       └── lib/
│
├── routes/
├── database/
└── tests/
```

---

# 2. Mandatory Principles

The agent MUST follow these principles.

## 2.1 Separation of Concerns

Each layer must have a clearly defined responsibility.

| Layer          | Responsibility                             |
| -------------- | ------------------------------------------ |
| Controller     | HTTP orchestration                         |
| Form Request   | Input validation                           |
| Resource       | API response transformation                |
| Domain Action  | One business operation                     |
| Domain Service | Reusable business logic                    |
| Model          | Entity, relationship, persistence behavior |
| DTO            | Typed data transfer                        |
| Enum           | Fixed domain values                        |
| Policy         | Authorization                              |
| Job            | Async processing                           |
| Event          | Domain/application event                   |
| Listener       | Event reaction                             |

---

# 3. Domain Structure

Use the following structure:

```text
app/Domains/

├── User/
│   ├── Models/
│   ├── Actions/
│   ├── Services/
│   ├── DTOs/
│   ├── Enums/
│   ├── Policies/
│   └── Exceptions/
│
├── Product/
│   ├── Models/
│   ├── Actions/
│   ├── Services/
│   ├── DTOs/
│   ├── Enums/
│   └── Exceptions/
│
└── Order/
    ├── Models/
    ├── Actions/
    ├── Services/
    ├── DTOs/
    ├── Enums/
    └── Exceptions/
```

Do not create domains or directories that are not needed.

Follow this principle:

> Create structure when complexity requires it, not because the architecture diagram contains it.

---

# 4. HTTP Layer

The HTTP layer must remain outside the domain.

```text
app/Http/

├── Controllers/
│   └── Api/
│
├── Requests/
└── Resources/
```

The domain must not directly depend on:

* Request
* Response
* Controller
* Route
* HTTP status codes
* Session
* View

Correct:

```text
Controller
    ↓
Request
    ↓
Action / Service
    ↓
Model
```

Incorrect:

```text
Domain Service
    ↓
Request
    ↓
Controller
```

---

# 5. Controller Rules

Controllers must remain thin.

A controller is responsible only for:

1. Receiving the request
2. Using validated input
3. Calling a domain operation
4. Returning a response/resource

Example:

```php
public function store(StoreProductRequest $request)
{
    $product = $this->createProduct->execute(
        $request->validated()
    );

    return new ProductResource($product);
}
```

Avoid:

```php
public function store(Request $request)
{
    // validation
    // database query
    // business calculation
    // authorization logic
    // transaction
    // email
    // notification
    // response transformation
}
```

If a controller starts containing business logic, move that logic into the appropriate domain layer.

---

# 6. Form Request Rules

All non-trivial request validation must be placed in Form Request classes.

Example:

```php
class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
```

Controllers should not contain large validation definitions.

Use:

```php
$request->validated()
```

as the input boundary into the domain.

---

# 7. Action Rules

Use an Action for one clearly defined business operation.

Example:

```text
Actions/
├── CreateProduct.php
├── UpdateProduct.php
├── DeleteProduct.php
└── PublishProduct.php
```

Each Action should have one clear purpose.

Example:

```php
class CreateProduct
{
    public function execute(array $data): Product
    {
        return Product::create($data);
    }
}
```

If an operation contains multiple business steps, the Action may coordinate Services or other Actions when appropriate.

Do not create Actions that perform many unrelated operations.

---

# 8. Service Rules

Use a Service when business logic is:

* complex
* reused by multiple entry points
* responsible for coordinating multiple operations
* inappropriate to place directly inside a Model

Example:

```php
class OrderService
{
    public function createOrder(
        User $user,
        array $items
    ): Order {
        return DB::transaction(function () use ($user, $items) {
            // business logic
        });
    }
}
```

Do not create Services that merely wrap simple CRUD operations:

```php
public function find($id)
{
    return User::find($id);
}
```

If there is no meaningful business logic, using the Model/Eloquent directly may be simpler.

---

# 9. Repository Rules

Repositories are NOT mandatory.

Do not create this structure for every CRUD operation:

```text
Controller
    ↓
Service
    ↓
Repository
    ↓
Model
```

Use a Repository only when there is a clear reason, such as:

* very complex queries
* multiple data sources
* persistence abstraction is genuinely useful
* a specific architectural requirement

For simple Eloquent CRUD, use the Model/query builder directly.

---

# 10. DTO Rules

Use DTOs when data exchanged between layers becomes complex or requires a strongly typed structure.

Example:

```php
final readonly class ProductData
{
    public function __construct(
        public string $name,
        public int $price,
        public ProductStatus $status,
    ) {}
}
```

Do not create DTOs for every simple array without a real benefit.

---

# 11. Enum Rules

Use PHP Enums for domain values with a fixed set of valid values.

Example:

```php
enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
```

Avoid magic strings when the value represents an actual domain concept.

Prefer:

```php
$order->status = OrderStatus::Paid;
```

over:

```php
$order->status = 'paid';
```

---

# 12. Model Rules

Models are responsible for:

* database mapping
* relationships
* casts
* accessors/mutators when appropriate
* persistence-related behavior
* reusable query scopes

Example:

```php
class Product extends Model
{
    protected function casts(): array
    {
        return [
            'status' => ProductStatus::class,
            'price' => 'integer',
        ];
    }
}
```

Do not place the entire application's business process inside Models.

Avoid replacing a "fat controller" with a "fat model".

---

# 13. API Resource Rules

Do not expose Eloquent Models directly as the public API contract.

Use Resources:

```php
return new ProductResource($product);
```

Example:

```php
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'status' => $this->status,
        ];
    }
}
```

The frontend must depend on the API contract, not the database structure.

---

# 14. Frontend Architecture

The frontend should also follow a domain-oriented structure.

```text
resources/js/

├── domains/
│   ├── user/
│   │   ├── api/
│   │   ├── components/
│   │   ├── pages/
│   │   ├── types/
│   │   └── composables/
│   │
│   ├── product/
│   │   ├── api/
│   │   ├── components/
│   │   ├── pages/
│   │   ├── types/
│   │   └── composables/
│   │
│   └── order/
│       ├── api/
│       ├── components/
│       ├── pages/
│       ├── types/
│       └── composables/
│
├── components/
├── layouts/
├── lib/
└── types/
```

Whenever practical, frontend domains should mirror backend domains:

```text
BE                                  FE

Domains/Product/                    domains/product/
Domains/User/                       domains/user/
Domains/Order/                      domains/order/
```

---

# 15. Frontend API Boundary

The frontend must never access the database directly.

Use a domain API client:

```text
Component
    ↓
Composable / Domain API
    ↓
HTTP Client
    ↓
Laravel API
```

Example:

```ts
export const productApi = {
    async getAll() {
        return http.get('/api/products')
    },

    async create(data: ProductData) {
        return http.post('/api/products', data)
    },
}
```

Components should not contain unnecessary HTTP implementation details.

---

# 16. Shared Frontend Components

Use:

```text
components/
```

for generic reusable components.

Example:

```text
components/
├── Button.vue
├── Modal.vue
├── DataTable.vue
├── Input.vue
└── Pagination.vue
```

Use:

```text
domains/product/components/
```

for Product-specific components.

Example:

```text
domains/product/components/
├── ProductForm.vue
├── ProductTable.vue
└── ProductStatusBadge.vue
```

Rule:

> Generic UI → components/

> Domain-specific UI → domains/{domain}/components/

---

# 17. API Contract

The API must act as the boundary between frontend and backend.

Example:

```text
GET    /api/products
POST   /api/products
GET    /api/products/{product}
PUT    /api/products/{product}
DELETE /api/products/{product}
```

Request:

```json
{
    "name": "MacBook Pro",
    "price": 25000000
}
```

Response:

```json
{
    "data": {
        "id": 1,
        "name": "MacBook Pro",
        "price": 25000000,
        "status": "active"
    }
}
```

Do not make the frontend dependent on internal database columns.

---

# 18. Dependency Direction

Dependencies should flow toward business logic.

Preferred:

```text
Frontend
 ↓
HTTP
 ↓
Domain
 ↓
Infrastructure
```

Avoid:

```text
Domain
 ↓
HTTP Controller
```

The domain must not know how it is being invoked.

The same domain operation should be reusable from:

```text
API
Web
CLI
Queue
Command
Job
```

without changing the underlying business logic.

---

# 19. Authentication

Authentication belongs to the HTTP/application layer.

Domain logic should not retrieve the authenticated user directly from the global request:

```php
request()->user()
```

Instead, explicitly provide the user:

```php
$createOrder->execute(
    user: $request->user(),
    data: $data,
);
```

This allows the Action to be reused from APIs, CLI commands, Jobs, and tests.

---

# 20. Authorization

Use Policies/Gates for authorization.

Example:

```php
$this->authorize('update', $product);
```

Do not spread authorization rules throughout the application:

```php
if ($user->role === 'admin')
```

Authorization rules should have a clear and centralized ownership.

---

# 21. Transactions

If a business operation modifies multiple pieces of data that must remain consistent, use a database transaction around that operation.

Example:

```php
return DB::transaction(function () {
    // create order
    // create order items
    // update stock
});
```

Transactions should normally live at the boundary of the business operation, rather than being scattered across small methods.

---

# 22. Events and Jobs

Use Events when something has happened and other parts of the application may need to react.

Example:

```text
OrderCreated
```

Use Jobs for asynchronous work:

```text
SendOrderConfirmationEmail
GenerateInvoice
SyncToExternalService
```

Do not put long-running asynchronous processes inside Controllers.

---

# 23. Testing Strategy

Each domain should be testable without always going through the browser.

Recommended structure:

```text
tests/
├── Unit/
│   └── Domains/
│       ├── User/
│       ├── Product/
│       └── Order/
│
└── Feature/
    └── Api/
        ├── Users/
        ├── Products/
        └── Orders/
```

Use Unit Tests for domain/business logic.

Use Feature Tests for API contracts and HTTP behavior.

---

# 24. Feature Development Workflow

When asked to implement a new feature, the agent MUST follow this workflow.

## Step 1 — Identify the Domain

Determine which domain owns the feature.

Example:

```text
"User can create an order"

→ Order Domain
```

Do not start by creating a Controller.

---

## Step 2 — Identify the Business Operation

Determine the actual business operation.

Examples:

```text
CreateOrder
CancelOrder
PayOrder
CompleteOrder
```

---

## Step 3 — Design the Domain

Determine which components are actually needed:

```text
Model
Action
Service
DTO
Enum
Policy
Exception
```

Create only what is necessary.

---

## Step 4 — Design the API Contract

Define:

```text
HTTP method
URL
request payload
response payload
validation
authorization
error response
```

before implementing the frontend.

---

## Step 5 — Implement Backend

Recommended implementation order:

```text
Migration
    ↓
Model
    ↓
Enum / DTO
    ↓
Action / Service
    ↓
Request
    ↓
Resource
    ↓
Controller
    ↓
Route
```

---

## Step 6 — Implement Frontend

Then implement:

```text
Types
    ↓
API client
    ↓
Composable/state
    ↓
Domain components
    ↓
Page
```

---

## Step 7 — Test

At minimum, add appropriate tests for:

```text
Domain/business logic
API behavior
Frontend behavior
```

based on the project's existing testing stack.

---

# 25. Existing Code

When modifying existing code:

1. Do not perform large refactors without a clear reason.
2. Follow the existing architecture when it is already consistent.
3. If the existing architecture is poor, refactor only the relevant area.
4. Do not move the entire project merely to satisfy this pattern.
5. Preserve backward compatibility when required.
6. Do not introduce a new abstraction when a simpler solution is sufficient.

---

# 26. Anti-Patterns

The agent MUST avoid the following.

## Fat Controller

```php
public function store()
{
    // 100+ lines of business logic
}
```

## Fat Component

A frontend component should not contain all of:

```text
HTTP requests
validation
business calculations
state management
formatting
UI
```

in one large file.

## Generic God Service

Avoid:

```text
AppService.php
CommonService.php
ManageService.php
UtilityService.php
```

containing unrelated business logic.

## Premature Repository

Do not create repositories simply because "Clean Architecture" suggests them.

## Generic Helpers

Do not put domain/business logic into:

```text
helpers.php
```

merely because it is accessible from many places.

Business logic belongs to the domain that owns it.

---

# 27. Naming Convention

Use names that describe business intent.

Good:

```text
CreateOrder
CancelOrder
CalculateOrderTotal
ApproveRefund
PublishProduct
```

Avoid vague names:

```text
ProcessData
HandleData
ManageOrder
DoSomething
CommonService
```

Class names should clearly communicate intent.

---

# 28. Simplicity Rule

Architecture must follow application complexity.

For a simple feature:

```text
Controller
    ↓
Request
    ↓
Model
```

is acceptable.

For more complex business logic:

```text
Controller
    ↓
Request
    ↓
Action
    ↓
Service
    ↓
Model
```

is acceptable.

For a complex domain:

```text
Controller
    ↓
Request
    ↓
Action
    ↓
DTO
    ↓
Domain Services
    ↓
Models
```

is acceptable.

Do not add abstractions without a clear benefit.

---

# 29. Agent Decision Rules

Before creating a new file, the agent must ask:

1. Is this file actually necessary?
2. Which domain owns this logic?
3. Is this logic HTTP-specific?
4. Is this logic business-specific?
5. Is this logic reusable?
6. Would an Action be more appropriate than a Service?
7. Is a Repository genuinely necessary?
8. Is this component generic or domain-specific?
9. Does this change affect the API contract?
10. Does a test need to be added or updated?

---

# 30. Expected Final Architecture

Target architecture:

```text
project/
│
├── app/
│   ├── Domains/
│   │   ├── User/
│   │   ├── Product/
│   │   ├── Order/
│   │   └── Payment/
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   ├── Requests/
│   │   └── Resources/
│   │
│   └── Support/
│
├── resources/
│   └── js/
│       ├── domains/
│       │   ├── user/
│       │   ├── product/
│       │   ├── order/
│       │   └── payment/
│       │
│       ├── components/
│       ├── layouts/
│       └── lib/
│
├── routes/
│   ├── api.php
│   └── web.php
│
├── database/
│
└── tests/
    ├── Unit/
    └── Feature/
```

## Final Rule

When developing a feature:

> **Organize code by business domain, separate HTTP concerns from business logic, keep frontend and backend separated by an API boundary, and introduce abstractions only when complexity justifies them.**

The agent must prioritize:

1. Clarity
2. Maintainability
3. Testability
4. Low coupling
5. Explicit business intent
6. Simplicity

over excessive use of architectural patterns.
