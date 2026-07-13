# Project Business Documentation

## Purpose

This document describes the current business model implemented in this Laravel educational SaaS project after the latest pulled changes.

It is intended for developers and AI coding agents before they modify the project. Treat this as the current business map, not as a full product specification.

## Review Snapshot

- Reviewed branch: `main`
- Reviewed HEAD: `582d1b8 User Auth`
- Updated on: 2026-07-13
- Application stack in the pulled code: Laravel 12, Filament admin panel, Livewire, Sanctum, Spatie Translatable.

The previous version of this file described a mostly fresh Laravel app. That is no longer accurate. The pulled code now contains a large domain schema, Eloquent models, tenant filtering, a Filament dashboard, provider website routing, and CRUD resources for most business entities.

## Pulled Changes Reviewed

Recent business-significant commits reviewed:

- `e49ec9e Inital database`
  - Added the main education SaaS database structure, enums, models, seeders, Filament panel provider, academy website controller, and static academy website template files.
- `7b1f0da Enhance Filament resources`
  - Added generated Filament resources, split resource forms/tables/pages, and introduced shared base Filament classes.
- `184fd1d Update Filament 5`
  - Updated Filament-related dependencies/assets.
- `d2cb002 Filter By tenet`
  - Added `FiltersByTenant` and tenant-aware query behavior for models/resources.
- `f3dec16 Enhance User Tables`
  - Added provider plans/subscriptions and provider access gating.
- `57b3de2 User Cycle`
  - Reworked account membership into `employees`, added the custom Filament login page, and refined account/user/provider/student CRUD forms.
- `582d1b8 User Auth`
  - Updated user authentication/dashboard access pieces.

## Current Business Model

The project is a multi-tenant educational SaaS platform for recorded-content learning.

The same core model supports:

- Academies that operate their own educational platform.
- Standalone teachers that operate their own teacher platform.
- Students and parents using provider websites.
- SaaS owners/admins managing global platform data and providers.

The project should continue using shared domain entities such as `Course`, `Lesson`, `AccountSubject`, and `Provider`. Do not create duplicate domains like `AcademyCourse`, `TeacherCourse`, `AcademyLesson`, or `TeacherLesson` unless the business model is intentionally changed.

## Explicitly Out of Scope

This platform does not currently support live educational sessions.

Do not add or document implementation for:

- Live classes
- Zoom, Google Meet, Agora, Zego, or similar meeting systems
- Meeting links or passwords
- Live-session attendance
- Real-time virtual classrooms

The learning model is based on recorded videos, uploaded files, assignments, exams, quizzes, and downloadable resources.

## Main Application Surfaces

### Filament Dashboard

The dashboard is mounted at `/admin` through `App\Providers\Filament\AdminPanelProvider`.

Important behavior:

- Uses a custom login page: `App\Filament\Pages\Auth\Login`.
- Requires authentication.
- Applies `EnsureCurrentAccount:dashboard`.
- Dashboard access is limited to active accounts of type:
  - `saas_owner`
  - `academy`
  - `academy_teacher`
  - `standalone_teacher`
- Non-SaaS-owner dashboard accounts also require an active provider subscription.
- Uses Cairo font, full-width content, amber primary color, and English/Arabic locale switching.

### Provider Website

Provider websites are served by `App\Http\Controllers\AcademySiteController`.

Route shape:

```php
{accountSubdomain}.{config('almanasa.root_domain')}/{page?}
```

Current behavior:

- Finds a provider by `subdomain`.
- Requires the provider to have an active subscription.
- Allows provider type `academy` or `standalone_teacher`.
- Serves static HTML templates from `public/academy`.
- Rewrites asset paths and page title/name placeholders.

This is currently a template-serving website layer, not a full dynamic frontend.

## Tenant, Provider, and Account Model

### Provider

`Provider` is the main business tenant/platform entity.

Provider types:

- `academy`
- `standalone_teacher`

Provider stores platform-level settings such as:

- Name, slug, subdomain, optional custom domain
- Owner user
- Country/city
- Website, registration, chat, and payment enablement flags
- Tax percentage
- Active/inactive state
- Translatable bio and address

Provider access to website/dashboard functionality is gated by active provider subscriptions.

### Account

`Account` is the access container used for dashboard/website authorization and current context.

Account types:

- `saas_owner`
- `academy`
- `academy_teacher`
- `standalone_teacher`
- `student`
- `parent`

Dashboard-capable accounts:

- `saas_owner`
- `academy`
- `academy_teacher`
- `standalone_teacher`

Website-capable accounts:

- `student`
- `parent`

Accounts have:

- Optional `provider_id`
- `owner_user_id`
- `type`
- `is_active`
- Optional `approved_at`

Current code has `approved_at`, but the pulled HEAD does not include a complete public provider registration + admin approval workflow. Access is currently enforced mainly through active accounts and active provider subscriptions.

### Employees

The current account membership concept is named `Employee`.

Important naming note:

- The migration file is named `create_account_memberships_table`.
- The actual table created is `employees`.
- The model/resource name is `Employee`.

Use `employees` as the current account-membership implementation.

An employee links:

- `account_id`
- `user_id`
- `predefined_role`
- Optional custom `role_id`
- Optional creator user
- Status
- Joined timestamp

Predefined employee roles:

- `owner`
- `admin`
- `teacher`
- `student`
- `parent`
- `staff`
- `support`

There is also a `roles` table/model for provider-specific custom roles.

## Current Account Resolution and Access Rules

`EnsureCurrentAccount` resolves the active account from:

1. `account_id` request input
2. `X-Account-Id` header
3. `current_account_id` session value
4. First accessible account

An account is accessible when:

- The authenticated user owns the account, or
- The user has an active employee row for the account.

For the dashboard surface:

- Account must be active.
- Account type must support dashboard access.
- SaaS owner accounts are allowed without provider subscription.
- Academy, academy teacher, and standalone teacher accounts require an active provider subscription.

For the website surface:

- Account must be active.
- Account type must be `student` or `parent`.

The middleware writes the resolved `current_account` and `current_provider` into request attributes and session.

## Tenant Filtering

`App\Concerns\FiltersByTenant` is the current tenant-scoping mechanism.

Important behavior:

- SaaS owner accounts see unscoped data.
- Non-SaaS-owner accounts are scoped by provider/account context.
- Models with `provider_id` are filtered by current provider.
- Models with `teacher_account_id` are filtered by current teacher account.
- Models can define `tenantRelations` for relationship-based tenant filtering.
- Filament resources inherit tenant-aware `getEloquentQuery()` behavior through this trait when applicable.

This is a broad tenant filtering mechanism. Authorization policies and fine-grained permissions are still not clearly implemented across all operations.

## Users and Profiles

### Users

The `users` table is no longer Laravel's default email-only table.

Current user fields include:

- First name
- Last name
- Unique phone
- Optional dial country code
- Nullable password
- OTP
- Date of birth
- Verification timestamp
- Active flag
- Remember token
- Soft deletes

The `User` model:

- Implements Filament access checks.
- Uses active employee rows and active accounts for dashboard access.
- Has owned accounts.
- Has student and parent profiles.
- Appends computed `name` from first/last name.
- Casts password as hashed.

Important current gap:

- The schema is phone-first.
- If login is expected by email, that is not represented in the current `users` migration.

### Student and Parent Profiles

The domain includes:

- `StudentProfile`
- `ParentProfile`
- `ParentStudent`

These support student academic data, parent data, and linking parents to students.

## Education Catalog

Global education/catalog data includes:

- Countries
- Cities
- Education stages
- Grades
- Subjects
- Grade subjects
- Tracks

`GradeSubject` is the global grade/subject pairing.

`AccountSubject` is provider-specific and represents which global grade-subject offering a provider has enabled.

## Academy and Teacher Assignment Model

Academy/provider teacher assignment is represented by:

- `AcademyTeacher`
- `AcademyTeacherGradeSubject`

`AcademyTeacher` links a provider to a teacher account.

`AcademyTeacherGradeSubject` links that teacher assignment to a provider's `AccountSubject`.

For standalone teachers, the same account/provider/course model should still be used. Avoid creating a separate standalone-teacher content model.

## Courses and Learning Content

Current learning content entities include:

- `Course`
- `CourseOutcome`
- `CourseUnit`
- `Lesson`
- `LessonItem`
- `Assignment`
- `Exam`

Course records belong to:

- Provider
- Account subject
- Optional teacher account

Course fields include title, slug, description, thumbnail, term, price, monthly price, weekly lecture count, status, featured flag, and publication date.

Lesson items support mixed recorded-content/resource types through `LessonItemType`, including video/file/assignment/exam style content.

## Assessments, Attempts, and Progress

Assessment and student-work entities include:

- `Question`
- `QuestionOption`
- `StudentAttempt`
- `StudentAnswer`
- `LessonProgress`
- `DownloadLog`
- `CourseReview`

Student attempts are polymorphic by `attemptable_type` and `attemptable_id`, allowing attempts against multiple assessment-like entities.

Progress and download tracking exist at schema/model/resource level.

## Sales, Payments, and Subscriptions

Commerce entities include:

- `Package`
- `PackageCourse`
- `Subscription`
- `StudentEnrollment`
- `Cart`
- `CartItem`
- `Order`
- `OrderItem`
- `Payment`
- `PaymentCode`
- `Coupon`

Payments support fields for:

- Method
- Status
- Amount
- Transaction reference
- Payment code
- Sender phone
- Transfer image
- Gateway response
- Paid timestamp
- Manual review user/timestamp

Current implementation appears to provide persistence and Filament CRUD surfaces. Complete payment gateway/manual approval workflows should be verified before relying on them as product-complete.

## Provider SaaS Plans and Subscriptions

Provider SaaS billing is represented by:

- `ProviderPlan`
- `ProviderSubscription`

Provider subscription statuses are used for access gating.

Active provider access requires:

- Status `trialing` or `active`
- `starts_at` is null or in the past
- `ends_at` is null or in the future
- `trial_ends_at` is null or in the future

This subscription is what unlocks dashboard access for non-SaaS-owner provider accounts and website access for provider subdomains.

## Communication, Website Content, and Operations

Communication and website/operations entities include:

- `ChatRoom`
- `ChatMember`
- `ChatMessage`
- `Notification`
- `SupportTicket`
- `SupportTicketReply`
- `Banner`
- `Testimonial`
- `HonorBoardEntry`
- `AuditLog`

Chat is modeled around provider/course/lesson rooms and members/messages.

Support tickets and replies are persisted.

Website content entities exist for provider-facing public pages, but the current public website controller still serves static template files.

## Filament Admin Resources

Filament resources exist for nearly all major domain models.

Navigation groups currently include:

- Identity & Accounts
- Project data
- Education Setup
- Learning Content
- Operations
- Communication & Website
- Sales & Payments
- Students & Families

The resource layer is broad, but many resources appear generated/basic. Treat CRUD availability as administrative scaffolding, not necessarily as complete business workflow implementation.

## Implemented vs Workflow Complete

Implemented now:

- Large database schema for the intended education SaaS domain.
- Eloquent models and relationships for most business entities.
- Tenant filtering based on current account/provider.
- Filament dashboard resources for most models.
- Provider subscription gating for dashboard and website access.
- Static provider website template serving by subdomain.

Not clearly complete in current HEAD:

- Public provider registration with subscription selection and admin approval.
- Student/parent public registration/login flow wired to the static templates.
- Complete checkout/payment verification workflow.
- Fine-grained role/permission policy enforcement.
- Production-ready website rendering from database content.
- Full business actions/services for enrollment, payment approval, publishing, assessment grading, and notifications.

## Important Development Notes

- Use `Provider` as the tenant/platform entity.
- Use `Account` as the access/context entity.
- Use `Employee` for account membership.
- Use `AccountSubject` for provider-enabled grade-subject offerings.
- Use `AcademyTeacher` and `AcademyTeacherGradeSubject` for teacher assignment.
- Keep academy and standalone-teacher logic on the shared provider/account/course model.
- Do not add live-session concepts.
- When adding dashboard features, verify both tenant filtering and authorization. Tenant filtering alone is not a complete permission system.
- When accepting user input, review current models using `$guarded = []`; avoid unsafe mass assignment in controllers/actions.
