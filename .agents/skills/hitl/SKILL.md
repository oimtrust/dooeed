---
name: hitl
description: Require human-in-the-loop confirmation before creating or modifying deliverables, files, code, configuration, or external content. Present three numbered options and allow custom text input. Use for creation and modification tasks in this project.
---

# Human in the Loop (HITL)

## Confirmation Before Changes

Before creating or modifying anything for the user, explain the proposed result and request their approval. Read-only inspection and analysis may proceed to make the proposal concrete, but do not write files, implement changes, or execute mutations before approval.

This includes code, documentation, database changes, configuration, generated artifacts, and external content. Do not create an implementation or a temporary draft file as a way to bypass confirmation. Describe the proposed change in the conversation instead.

The user's explicit instructions take precedence. Approval already granted for a specific scope remains valid: proceed within that scope without asking again for each file or routine implementation step. The instruction to create this HITL skill authorizes creating the skill itself.

## Required Question Format

Give a short, specific description of what will be created or changed, its location, and any material effects. Then provide exactly three numbered choices:

1. **Proceed with the proposal** — Approve the described scope.
2. **Revise the proposal** — Discuss changes before implementation.
3. **Cancel** — Do not carry out the proposal.

Also allow a free-text answer for instructions beyond these choices. Do not restrict the user to choosing a number.

When the interface provides a clarification or approval tool with a free-text field, use it to present the three choices. For example, with `request_user_input_async`, supply one self-contained question and three `options` strings prefixed with `1.`, `2.`, and `3.`. The interface supplies the additional free-text field; do not add a fourth option named Other.

If that interface is unavailable, ask in the conversation using the same three choices, followed by: **Custom input: write your instructions here.** Do not imply that a real form field exists when only a text response is available.

Use the user's language for the question and choices.

## Waiting and Interpreting the Answer

- Wait for an explicit response before performing the proposed creation or modification. Silence, a timeout, or a preselected option is not approval.
- While waiting, continue only useful read-only work that does not depend on the answer.
- Option 1 authorizes the described proposal.
- Option 2 requests revision, not implementation. Use any accompanying input to refine the proposal; ask for approval of the revised scope before proceeding.
- Option 3 cancels the proposal.
- A free-text answer may approve, modify, or reject the proposal. Follow clear approval within its stated scope. If the intent is ambiguous, clarify before making changes.
- After approval, complete the authorized work and verification without repeated confirmation for routine steps. Ask again if a new decision materially expands the scope, effects, or external destination beyond that approval.
- HITL approval does not replace sandbox permissions or other mandatory execution approvals.

## Example

Proposal: Add an admin API guide in `ADMIN_API.md`, including authentication, cURL examples, and web access instructions.

1. Proceed with the proposal.
2. Revise the proposal.
3. Cancel.

Custom input: You may specify a different filename, language, or documentation scope.

Do not create the file until the user approves the proposal.
