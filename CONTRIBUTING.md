# Contributing

When contributing to this repository, please first discuss the change you wish to make via issue,
email, or any other method with the owners of this repository before making a change. 

Please note we have a code of conduct, please follow it in all your interactions with the project.

## Merge request guidelines

If you can, please submit a merge request with the fix or improvements
including tests. If you don't know how to fix the issue but can write a test
that exposes the issue we will accept that as well. In general bug fixes that
include a regression test are merged quickly while new features without proper
tests are least likely to receive timely feedback. The workflow to make a merge
request is as follows:

1. Fork the project into your personal space
1. Create a feature branch, branch away from `master`
1. Write code and charts changes.
1. If you have multiple commits please combine them into a few logically
  organized commits by [squashing them][git-squash]
1. Push the commit(s) to your fork
1. Submit a merge request (MR) to the `master` branch
  1. Your merge request needs at least 1 approval but feel free to require more.
    For instance if you're touching multiple charts, replacing a provider, or
    altering an behavior on a global level.
  1. You don't have to select any approvers, but you can if you really want
    specific people to approve your merge request.
1. The MR title should describe the change you want to make
1. The MR description should give a motive for your change and the method you
   used to achieve it.
  1. Mention the issue(s) your merge request solves, using the `Solves #XXX` or
    `Closes #XXX` syntax to auto-close the issue(s) once the merge request will
    be merged.
1. If you're allowed to, set a relevant milestone and labels
1. Be prepared to answer questions and incorporate feedback even if requests
   for this arrive weeks or months after your MR submission
  1. If a discussion has been addressed, select the "Resolve discussion" button
    beneath it to mark it resolved.
1. When writing commit messages please follow
   [these](http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html)
   [guidelines](http://chris.beams.io/posts/git-commit/).

Please keep the change in a single MR **as small as possible**. If you want to
contribute a large feature think very hard what the minimum viable change is.
Can you split the functionality? Can you do part of the refactor? The increased
reviewability of small MRs that leads to higher code quality is more important
to us than having a minimal commit log. The smaller an MR is the more likely it
is it will be merged (quickly). After that you can send more MRs to enhance it.
The ['How to get faster PR reviews' document of Kubernetes](https://github.com/kubernetes/community/blob/master/contributors/devel/faster_reviews.md) also has some great points regarding this.

### Coding conventions

Start reading our code and you'll get the hang of it. However basic standard 
should be [psr-12](https://www.php-fig.org/psr/psr-12/). You can also use our
GIT [pre-commit-hook](https://gitlab.alembiq.net/larp/bistro/raw/master/doc/pre-commit) for automatic check.

### Database changes

