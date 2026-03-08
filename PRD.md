# PRD / Project Plan
## Project: Prompt Repository + Iteration Lab
## Stack: Laravel + Filament
## Status: Draft for coding agent execution

---

# 1. Product Summary

Build a Laravel Filament application that serves as a centralized prompt repository, version-control system, and testing lab for AI prompts.

Users will be able to:
- create prompts
- edit prompts
- save prompt versions automatically
- browse version history
- roll back to previous versions
- branch from any prior version
- organize prompts by adapter category
- test prompt versions against connected AI services
- compare outputs across adapters and runs

The system’s two first-class resources are:
- Prompts
- Adapters

Adapters represent connected AI services or model endpoints and belong to one of the following categories:
- coding
- chat
- image
- video

Prompts are versioned and architected using event sourcing so that every meaningful edit becomes part of the permanent history of the prompt aggregate.

---

# 2. Product Vision

Create a Git-like workflow for prompts inside a business-friendly Laravel Filament app.

The app should help users move from:
- rough prompt drafts
to
- tested, versioned, reproducible prompt assets

The app should support both:
- prompt management
- prompt experimentation

This is not just a CRUD app. It is a prompt lifecycle system.

---

# 3. Primary Goals

## Business Goals
- Create a reusable internal tool for prompt engineering workflows
- Reduce prompt loss, duplication, and ad hoc experimentation
- Make prompt development auditable and reproducible
- Enable teams to standardize prompt testing across providers and media types

## Product Goals
- Treat prompts as durable assets
- Preserve complete edit history
- Allow rollback and branching without data loss
- Allow prompt testing against multiple AI providers through adapter abstractions
- Provide a clean Filament admin UX for non-developer power users

## Technical Goals
- Use Laravel Filament as the application shell and admin UI
- Use event sourcing for prompt lifecycle/history
- Keep adapters pluggable and category-driven
- Support future expansion into evaluation, scoring, collaboration, and approvals

---

# 4. Non-Goals (Phase 1)

The following are explicitly out of scope for initial release unless otherwise noted:
- real-time collaborative editing
- full team commenting and annotations
- automated prompt scoring/evals
- dataset management for eval suites
- scheduled or batch prompt test jobs
- public prompt marketplace
- semantic search over prompt history
- complex branching graphs visualization like GitHub desktop
- multi-tenant billing/subscriptions
- prompt execution orchestration beyond direct test runs

---

# 5. Core Concepts

## 5.1 Prompt
A Prompt is the logical root entity users manage over time.

A Prompt has:
- a stable identity
- metadata
- a current active version reference
- a lineage/history of versions
- optional branches
- one or more test runs

A Prompt is not a single text blob. It is a versioned asset.

## 5.2 Prompt Version
A Prompt Version is an immutable snapshot of a prompt at a point in time.

A version may include:
- title
- description
- system prompt
- user prompt template
- developer/instruction prompt
- variables schema
- output expectations
- category tags
- notes / commit message
- source version reference
- branch label

Versions are immutable once created.

## 5.3 Branch
A Branch is a named line of development from a specific prompt version.

Examples:
- main
- concise-tone-experiment
- gpt-4o-image-pass
- claude-refactor-variant

A branch is not a separate prompt. It is a lineage path within a prompt’s version tree.

## 5.4 Adapter
An Adapter is a configured integration boundary for an AI service or model endpoint.

Adapters are first-class resources and belong to exactly one category:
- coding
- chat
- image
- video

Examples:
- OpenAI Chat Adapter
- Anthropic Chat Adapter
- OpenAI Image Adapter
- Replicate Video Adapter
- Local Ollama Coding Adapter

An adapter encapsulates:
- provider metadata
- authentication/config
- supported capabilities
- request mapping
- response normalization
- test execution rules

## 5.5 Test Run
A Test Run records the execution of a specific prompt version against a specific adapter.

A Test Run should capture:
- prompt version used
- adapter used
- input variables
- rendered prompt payload
- raw provider response
- normalized output
- latency
- token usage if available
- cost estimate if available
- status
- error payload if failed
- user who initiated run
- timestamp

---

# 6. Key User Types

## 6.1 Prompt Author
Creates and iterates on prompts, tests them, and manages versions.

## 6.2 Technical Admin
Configures adapters, credentials, provider settings, and access rules.

## 6.3 Reviewer (future-ready)
Inspects prompt history and test results, but may not edit adapters.

Phase 1 can ship with a simple user model plus roles/permissions scaffold.

---

# 7. Primary User Stories

## Prompt Creation
- As a user, I can create a new prompt with initial metadata and content
- As a user, I can assign or associate a prompt with one or more adapter categories
- As a user, I can save my initial draft as version 1

## Prompt Iteration
- As a user, each edit I make to a prompt should produce a new version rather than mutating history
- As a user, I can add a version note describing what changed
- As a user, I can see the full history of versions for a prompt
- As a user, I can open any prior version and inspect its contents

## Rollback / Restore
- As a user, I can restore a previous version as the active working version
- As a user, rollback should not delete history
- As a user, rollback should create a new version event representing the restore action

## Branching
- As a user, I can branch from any previous version
- As a user, branches should preserve ancestry
- As a user, I can continue iterating on a branch independently from main

## Adapter Management
- As an admin, I can create and manage adapters
- As an admin, I can assign an adapter category
- As an admin, I can store configuration required to connect to a provider
- As an admin, I can enable or disable an adapter

## Prompt Testing
- As a user, I can select a prompt version and test it against a compatible adapter
- As a user, I can supply test variables/inputs
- As a user, I can review the output, metadata, and raw response
- As a user, I can compare multiple test runs for the same prompt version across adapters

---

# 8. Functional Requirements

## 8.1 Prompt Resource
The Prompt resource must support:
- create prompt
- view prompt
- list prompts
- filter/search prompts
- edit prompt through version creation flow
- view version history
- restore previous version
- branch from version
- view branch lineage
- launch tests from current or selected version

### Prompt fields (logical prompt record)
- id
- name
- slug
- summary
- primary_category (optional)
- status (draft, active, archived)
- current_version_id
- created_by
- updated_by
- timestamps

### Recommended prompt metadata
- intended adapter categories (one or many)
- tags
- visibility (private/team, future-ready)
- evaluation notes
- default test variables JSON

## 8.2 Prompt Version Resource / View
Prompt versions are immutable records projected from event streams.

Fields:
- id
- prompt_id
- version_number
- branch_name
- parent_version_id
- source_event_id
- title
- system_prompt
- user_prompt_template
- developer_prompt
- variables_schema_json
- expected_output_format
- notes / commit_message
- created_by
- created_at

## 8.3 Adapter Resource
The Adapter resource must support:
- create adapter
- edit adapter
- enable/disable adapter
- test adapter connection
- classify adapter by category
- define provider-specific configuration

Fields:
- id
- name
- slug
- category (coding/chat/image/video)
- provider
- driver_class
- config_json
- is_active
- supports_streaming
- supports_variables
- supports_system_prompt
- supports_images
- supports_video
- supports_code
- created_by
- timestamps

## 8.4 Test Run Resource
The Test Run resource must support:
- create run from selected prompt version + adapter
- capture request and response artifacts
- store success/failure state
- display normalized result
- compare runs

Fields:
- id
- prompt_id
- prompt_version_id
- adapter_id
- initiated_by
- input_variables_json
- rendered_request_json
- raw_response_json
- normalized_output
- output_artifact_path (for image/video if applicable)
- status
- error_message
- latency_ms
- token_usage_input
- token_usage_output
- estimated_cost
- created_at

---

# 9. Event-Sourced Architecture for Prompts

## 9.1 Why Event Sourcing Here
Prompt history is a first-class concern. The system needs:
- perfect auditability
- immutable history
- rollback without destructive updates
- branching from historical states
- the ability to reconstruct prompt state at any version

Prompt editing is therefore not modeled as “update row in place.”
It is modeled as a stream of domain events on a Prompt aggregate.

## 9.2 Aggregate
Create a PromptAggregate as the source of truth for prompt lifecycle decisions.

The aggregate should enforce:
- valid version creation
- valid branch creation
- valid rollback rules
- ancestry integrity
- immutable history guarantees

## 9.3 Core Domain Events
At minimum:

- PromptCreated
- PromptMetadataUpdated
- PromptVersionCreated
- PromptVersionRestored
- PromptBranchCreated
- PromptRenamed
- PromptArchived
- PromptReactivated

Optional later:
- PromptTagged
- PromptVisibilityChanged
- PromptTestRequested
- PromptTestCompleted
- PromptTestFailed

## 9.4 Important Modeling Rule
Edits to prompt content should not directly mutate the current version row.
Instead:
- user submits edit
- domain command is issued
- aggregate validates
- PromptVersionCreated event is recorded
- projectors update read models
- current_version_id projection updates accordingly

## 9.5 Rollback Semantics
Rollback must be modeled as a new event, not as deletion or time travel mutation.

Example:
- version 3 exists
- user restores version 1
- system creates version 4 as a restored copy of version 1
- version 4 becomes current
- lineage records that version 4 was restored from version 1

This preserves history and keeps the system append-only.

## 9.6 Branching Semantics
Branching must also be event-driven.

Example:
- user selects version 2
- user creates branch “concise-pass”
- system emits PromptBranchCreated
- next edit on that branch emits PromptVersionCreated with:
  - parent_version_id = version 2
  - branch_name = concise-pass

Branching should preserve ancestry and allow later UI visualization.

---

# 10. Recommended Read Model Strategy

Because Filament prefers straightforward table/form interactions, use event sourcing for write-side domain truth and project into conventional read models for UI performance and simplicity.

## Read models to project
- prompts
- prompt_versions
- prompt_branches (optional explicit table)
- prompt_lineage_edges (optional for graph/tree views)
- prompt_version_snapshots (optional optimization)
- test_runs
- adapters

## Projection principle
- write side = aggregate + events
- read side = SQL-friendly tables for Filament resources

This gives:
- strong history guarantees
- easy admin UI
- easier search/filter/sort/reporting

---

# 11. Filament UX / Admin Design

## 11.1 Prompt List Page
Columns:
- name
- current version number
- current branch
- status
- intended categories
- updated at
- updated by

Filters:
- status
- category
- tag
- updated by
- date range

Actions:
- create prompt
- view
- edit (creates new version workflow)
- history
- branch
- archive
- test

## 11.2 Prompt Detail Page
Sections:
- prompt metadata
- active version content
- version history timeline
- branch selector
- recent test runs
- adapter compatibility panel

Actions:
- edit as new version
- restore version
- create branch from selected version
- test current version

## 11.3 Version History UI
Must allow:
- chronological list
- branch labels
- restore action
- branch-from-here action
- view diff summary (Phase 1 basic, future richer)
- inspect metadata for who changed what and why

## 11.4 Adapter List / Detail
Columns:
- name
- category
- provider
- active/inactive
- driver class
- last tested at (future)

Actions:
- create
- edit
- activate/deactivate
- test connection

## 11.5 Test Run UI
Must show:
- prompt version used
- adapter used
- rendered inputs
- output
- latency
- tokens/cost if available
- error details if failed

For image/video categories:
- render preview or downloadable artifact

---

# 12. Adapter Abstraction Design

## 12.1 Goal
Adapters should isolate provider-specific implementation details from the core prompt/versioning domain.

## 12.2 Contract
Create a common adapter interface, e.g.:
- validateConfig()
- supportedCapabilities()
- executeTest(PromptVersion $version, array $inputs): AdapterExecutionResult

## 12.3 Capability Flags
Different categories support different payloads and outputs.

Examples:
- chat adapter may support system + user messages
- coding adapter may support code-oriented instructions and structured output
- image adapter may support prompt + reference image params
- video adapter may support prompt + duration/aspect/model settings

## 12.4 Normalization
Each adapter should normalize results into a common result object where possible:
- status
- normalized_text_output
- artifact_url/path
- raw_response
- usage
- latency
- provider identifiers

---

# 13. Data Model (Conceptual)

## prompts
Logical resource table / read model

## prompt_versions
Immutable projected versions

## stored_events
Event store for prompt aggregate and other event-sourced entities if added later

## snapshots (optional)
Aggregate snapshots if needed for performance later

## adapters
Provider integration configurations

## test_runs
Execution history

## media_artifacts (optional)
Stores generated image/video file references

## tags / pivot tables (optional)
Prompt categorization

---

# 14. Permissions / Authorization

Phase 1 minimal roles:
- Admin
- Editor
- Viewer

## Admin
- manage adapters
- manage all prompts
- restore/branch/archive
- view all runs

## Editor
- create/edit prompts
- create branches
- run tests
- restore own prompts or permitted prompts

## Viewer
- read prompts/history/runs
- no editing

Use Laravel policies and Filament resource authorization.

---

# 15. Auditing / Observability

The app should maintain clear audit trails for:
- who created a prompt
- who created each version
- who restored a version
- who created a branch
- who ran a test
- which adapter was used
- what config was active at test time where appropriate

Because prompt content may evolve, the stored test run should preserve the exact prompt version used.

---

# 16. Success Metrics

Phase 1 success is measured by:
- users can create prompts reliably
- every edit produces a recoverable version
- rollback works without deleting history
- branching works from arbitrary historical versions
- adapters can be configured and used for tests
- test runs are stored and reviewable
- Filament UX is simple enough for daily use

Suggested operational metrics:
- number of prompts created
- average versions per prompt
- number of branches created
- number of test runs per week
- adapter success/failure rate
- median test latency
- rollback frequency

---

# 17. Risks / Design Considerations

## 17.1 Event Sourcing Complexity
Event sourcing adds architectural power but also complexity.
Mitigation:
- limit event sourcing to Prompt domain first
- use conventional tables for read side
- keep adapters and test runs non-event-sourced initially unless needed

## 17.2 Branching UX Complexity
Users may not intuitively understand branches.
Mitigation:
- default every prompt to a main branch
- keep branch creation explicit
- show simple lineage labels before building graph visualizations

## 17.3 Provider Heterogeneity
Chat, coding, image, and video providers behave differently.
Mitigation:
- define category-aware adapter contracts
- use capability flags
- normalize only what truly overlaps

## 17.4 Storage Growth
Test runs, raw responses, and media outputs may become large.
Mitigation:
- store artifacts in filesystem/object storage
- keep DB references only
- optionally truncate or archive large raw payloads later

## 17.5 Secret Management
Adapter credentials are sensitive.
Mitigation:
- encrypt at rest
- never expose secrets in Filament tables
- support masked fields and secure config handling

---

# 18. Recommended Technical Approach

## Backend
- Laravel latest stable
- PHP latest supported by Laravel/Filament target
- Filament for admin/resource UI
- Spatie Laravel Event Sourcing for prompt aggregate/event store
- Laravel queues for async test execution where needed
- Laravel policies for authz

## Storage
- MySQL or PostgreSQL for app data
- object storage/local disk abstraction for media outputs
- encrypted secrets handling for adapter config

## Event-Sourced Domain Scope
Phase 1 event-sourced domain:
- Prompt aggregate only

Conventional CRUD domains:
- adapters
- test runs
- users/roles

---

# 19. Suggested Build Phases

## Phase 1 — Foundation
- scaffold Laravel + Filament
- auth and roles
- adapters resource CRUD
- prompts read model
- prompt aggregate and events
- version creation flow
- version history UI

## Phase 2 — Restore and Branching
- rollback/restore actions
- branch creation flow
- branch-aware prompt version listing
- lineage display

## Phase 3 — Testing Lab
- adapter contract
- category-aware execution services
- test run creation/storage
- test results UI
- raw response inspection
- image/video artifact handling

## Phase 4 — Quality of Life
- diffs between versions
- compare runs
- prompt tags/search/filter improvements
- snapshots/performance tuning
- better lineage visualization

---

# 20. Open Questions for Product Owner

These should be resolved early:

- Should a prompt belong to one adapter category or many?
- Should a single prompt version be testable against adapters across categories, or only compatible categories?
- Are adapter credentials global, per-user, or per-workspace?
- Do we need team/multi-tenant support now or later?
- Should test runs be synchronous in UI or queued in background?
- Do we need prompt variables schema validation in Phase 1?
- Do we need side-by-side output comparison in Phase 1 or Phase 2?
- Should branching be lightweight labels or a full version tree model from day one?
- Should restoring a version always create a new version, or do we ever allow “set active pointer” behavior? Recommendation: always create a new version.
- Do we need provider/model-level overrides inside adapters?

---

# 21. Implementation Guidance for Coding Agent

Build the application with the following architectural principles:

1. Treat Prompt as an event-sourced aggregate root.
2. Never mutate prompt history destructively.
3. Every prompt content edit creates a new immutable version.
4. Restore is append-only and creates a new version from a historical version.
5. Branching preserves ancestry and should be represented explicitly.
6. Use projectors/read models so Filament can work with normal relational tables.
7. Keep Adapter architecture pluggable through contracts/drivers.
8. Separate provider-specific request/response mapping from domain logic.
9. Record complete execution context for every test run.
10. Optimize for correctness and traceability over premature abstraction.

---

# 22. Acceptance Criteria

## Prompt Versioning
- Creating a prompt stores an initial version
- Editing a prompt creates a new version record
- Prior versions remain immutable and visible
- Current active version is clearly identified

## Restore
- User can restore any prior version
- Restore creates a new version derived from prior content
- Audit trail shows restore origin

## Branching
- User can create a branch from any prior version
- New versions on a branch preserve parent linkage
- Branch name is visible in history and current state

## Adapters
- Admin can create adapters with category and config
- Inactive adapters cannot be used for new test runs
- Compatible adapters can be selected when running tests

## Testing
- User can run a prompt version against an adapter
- System stores prompt version, inputs, outputs, metadata, and status
- Failures are stored and inspectable

## UI
- Filament resources exist for prompts, adapters, and test runs
- Prompt detail view shows active version, history, branches, and test runs
- Version history supports restore and branch actions

---

# 23. One-Sentence Product Definition

A Laravel Filament app that gives teams a Git-like system for prompt creation, versioning, branching, rollback, and adapter-based AI testing across chat, coding, image, and video providers.

---

# 24. My Recommendation for Your Exact App

## Stack

**Laravel + Filament**

Use Filament as the full admin shell — resources, forms, tables, actions, and pages. This is the right choice for a data-dense internal tool.

## Event Sourcing

**Spatie Laravel Event Sourcing** for the Prompt aggregate.

This is the only domain that requires event sourcing. Everything else is conventional CRUD. Do not over-apply event sourcing.

## Read Models

Use standard Eloquent tables as read models for:

- `prompts` — logical prompt records, current version pointer, status, metadata
- `prompt_versions` — immutable projected snapshots of each version
- `prompt_branches` — named lines of development within a prompt's version tree
- `test_runs` — execution history for each prompt version / adapter pairing
- `adapters` — provider integration configurations

These tables are projected by Spatie projectors listening to domain events from the Prompt aggregate. Filament resources query these tables directly for all list, detail, filter, and search interactions.

## Why This Combination Works

- The write side (aggregate + events) guarantees immutable history, correct rollback semantics, and traceable lineage.
- The read side (Eloquent tables) gives Filament exactly what it needs: queryable, sortable, filterable relational rows.
- Spatie Laravel Event Sourcing handles the aggregate root, event store, projectors, and reactors — no need to build this infrastructure yourself.
- This is the simplest architecture that satisfies all first-class requirements without overengineering.

