Fabrication Framework
=====================

The Fabrication Framework implements an MVC pattern .

The central component, the model, A view can be any output representation of information, such as a chart or a diagram. Multiple views of the same information are possible, such as a bar chart for management and a tabular view for accountants. The third part, the controller, accepts input and converts it to commands for the model or view.[


Model, built in Object Relation Manager.

View, DOM based fabrication template engine.

Controller, managed by the fabrication framework, unless overridden.



--Workspace 

The workspace contain the users projects.



--Projects

Workspace Example 

To use the workspace helper for installing, creating projects add the library cli to your PATH change to suit your framework location (default is in workspace). 

Add the following to your ~/.bashrc configuration file.
PATH="$PATH:/home/YourName/workspace/project-fabrication-framework/library/cli/"

Ensure the changes are reset, now you should have access to your workspace.
source ~/.bashrc


) Creating a project.

$ workspace create project ProjectName


) Creating an application.

$ workspace create application ProjectName ApplicationName


) Creating an action.

$ workspace create action ProjectName ApplicationName ActionName


) Creating an action with template.

$ workspace create action ProjectName ApplicationName ActionName


) Creating an action with an object element mapped to html element.

...


