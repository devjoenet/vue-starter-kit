## PROJECT CODING RULES

### **IMPORTANT**: The rules under this `PROJECT CODING RULES` heading overrides **ANY** other rules within `AGENTS.md` or other guideline and skill files used to build it.
### Any rules in this listing (between this line and `### END OF PROJECT GUIDELINE OVERRIDES -`) SUPERSEDE ANY AND ALL OTHER GUIDELINES, SKILLS OR RULES. 

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
3. When completing any task provide a Markdown summary to the user formatted as shown in [this example](./.ai/guidelines/stubs/code-change-summary.stub).
4. Do not yet generate any GitHub workflows or CI.yml file in this project. We have not yet set it up, and it will consistantly fail as of the moment.
5. Backend code has no further opportunity for refactors/changes that move code that isn't a route, controller or middleware into a module. Requests, DTO's, actions, responses, models and module specific traits/concerns/contracts should all be located in an appropriate directory within a module.
6. There are no `DB::` class calls in the code that can be accomplished using Eloquent, or can have code added to a model allowing any DB calls to be removed.
7. As many exceptions as possible are not generic, but rather are named logically and allow a developer to find a module->class->method->line and context to track down the cause of the exception.
### END OF PROJECT GUIDELINE OVERRIDES - CONTINUE EVALUATING BEYOND THIS POINT AS NORMAL
