## PROJECT CODING RULES

### **IMPORTANT**: The rules under this `PROJECT CODING RULES` heading overrides **ANY** other rules within `AGENTS.md` or other guideline and skill files used to build it. Any rules in this listing (between this line and `### END OF PROJECT GUIDELINE OVERRIDES -`) SUPERSEDE ALL OTHER GUIDELINES, SKILLS AND RULES. 

1. Always use these flags when running CLI tools:
- Tests: `vendor/bin/phpunit --no-progress`
- PHPStan: `vendor/bin/phpstan analyse --no-progress --error-format=raw`
- Psalm: `vendor/bin/psalm --no-progress --no-suggestions --output-format=text`
- phpcs: `vendor/bin/phpcs --report=emacs -q`
- PHP-CS-Fixer: `vendor/bin/php-cs-fixer fix --show-progress=none -q -n`
- Rector: `vendor/bin/rector process --no-progress-bar --output-format=github`
2. Docblock format: When a docblock is encountered or written, it should be formatted/replaced with one that to follows these rules.
  - If it is a text only comment/note, it should be a single line.\
    - ie: `/** Determines the current asset version. */`
  - If it is an actual notation, and only one definition is being made, it should also be a single line.
    ```php
      /** @param  array<int, string>  $ruleNames */
    ```
    ```php
    /** @return array{user: array<string, mixed>|null, roles: list<string>, permissions: list<string>} */
    ```
  - If making multiple notations, format to the Laravel standard.
    ```php
    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    ```
3. Modules
  - Module directories should live under the top-level `Modules` directory.
  - Each module may contain the following directories
    - Actions
    - Commands
    - Contracts
    - DTOs
    - Events
    - Exceptions
    - Listeners
    - Models
    - Requests
    - Responses
    - Tests
  - Creating directories beyond these need to clarify with the user first.
  - Modules should be relatively self-sufficient with the exception of calling any shared classes.
  - Classes in `Support` and `Queries`: Support classes should be refactored to one or more `Actions` and queries should become an action or applied as a function within a model.
  - With the exception of the `App`/`Transport` classes, classes that are directly related to a module should be moved there.
  - Package-native modules live under top-level `Modules/{Domain}` and own their internal `app/`, `routes/`, `resources/`, and `tests/` directories.
  - Keep module logic within `Modules/{Domain}/app/**` and avoid recreating the old `app/Modules` tree.
4. When completing any task provide a Markdown summary to the user formatted as shown in [this example](./.ai/guidelines/stubs/code-change-summary.stub).
5. Backend code has no further opportunity for refactors/changes that move code that isn't a route, controller or middleware into a module. Requests, DTO's, actions, responses, models and module specific traits/concerns/contracts should all be located in an appropriate directory within a module.
6. There are no `DB::` class calls in the code that can be accomplished using Eloquent, or can have code added to a model allowing any DB calls to be removed.
7. As many exceptions as possible are not generic, but rather are named logically and allow a developer to find a module->class->method->line and context to track down the cause of the exception.
8. When a Spatie Laravel Data class already represents a payload, return the DTO or a typed collection/paginator of DTOs instead of flattening it with `->all()`. Only convert to arrays when a framework or third-party API explicitly requires an array, and update contracts and callers to expect the DTO object or collection directly.
9. Before generating any GitHub workflows or CI code, Ask the user that they are or aren't required. If they have not setup their CI/deployment tools, this could cause failures in their pipeline. This rule may be replaced with one based on that user response if they would like.
10. Input-like form controls with floating labels should use the label as the resting placeholder until the control is active, open, focused, or filled. Do not render a separate visible placeholder that competes with the label in the empty state. This applies to standard inputs, selects, date pickers, comboboxes, and any custom field component following the shared input pattern.
11. All classes within the project should leverage Laravel 13's expanded attribute support. This includes:
   - Eloquent Models
    `#[Table]: Defines the table name, primary key, and timestamp status.`
    `#[Fillable]: Replaces the $fillable protected property for mass assignment.`
    `#[Visible] / #[Hidden]: Controls which data is included in JSON serialization.`
    `#[ObservedBy]: Connects an Observer class directly to the model.`
    `#[ScopedBy]: Automatically applies a Global Scope.`
   - Queue & Background Jobs
    `#[Tries]: Sets the maximum number of attempts for a job.`
    `#[Timeout]: Defines the maximum execution time in seconds.`
    `#[Connection] / #[Queue]: Specifies the driver (Redis, Database) and queue name.`
    `#[FailOnTimeout]: Forces a failure state if the job exceeds its time limit.`
   - Artisan Console Commands
    `#[Signature]: Defines the command name and arguments (e.g., app:sync {user}).`
    `#[Description]: Provides the help text shown in the Artisan list.`
    `#[Scheduled]: Defines the cron or human-readable schedule (e.g., daily).`
    `#[OnOneServer]: Ensures a task only runs on a single node in a cluster.`
   - Routing & Controllers
    `#[Middleware]: Applies middleware to specific methods or the entire controller.`
    `#[Authorize]: Performs a policy check before the method executes.`
    `#[Validate]: Handles inline request validation logic.`
    `#[MapWith]: Maps route parameters directly to specific model attributes.`

### END OF PROJECT GUIDELINE OVERRIDES - CONTINUE EVALUATING BEYOND THIS POINT AS NORMAL
