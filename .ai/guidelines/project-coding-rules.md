## PROJECT CODING RULES

### **IMPORTANT**: The rules under this `PROJECT CODING RULES` heading overrides **ANY** other rules within `AGENTS.md` or other guideline and skill files used to build it.

1. Docblock format: When a docblock is encountered or written, it should be formatted/replaced with one that to follows these rules.
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
2. Modules
  - Module directories should be kept within the app directory.
  - Each module may contain the following directories
    - Actions
    - Commands
    - Contracts
    - DTOs
    - Exceptions
    - Models
  - Creating directories beyond these need to clarify with the user first.
  - Modules should be relatively self-sufficient with the exception of calling any shared classes.
  - Classes in `Support` and `Queries` should be refactored to be in `Actions`.
  - With the exception of the `App`/`Transport` classes, classes that are directly related to module should be moved there.
  - Modules should only be one directory deep under `app/Modules` and not grouped beyond this. 
3. When finished with coding tasks, please provide a Markdown summary, formatted as shown in [this example](./.ai/guidelines/stubs/code-change-summary.stub).
