# Git Automated Mirror

A php script that copies a git repository A to repository C (using a working repository B).

## Concept
The main idea is to fetch every ref (branch, tags) from repo _A_ (the **source**) and push it to repo _B_ (the **mirror**).
Optional the script merges a local branch into each branch and tag of _A_ before pushing it to _B_.

## Prerequisite
 * PHP 5.4
 * Linux based OS (Other Unix like OS might work as well)

You need a local repository and SSH access to both remote repositories (HTTPS is not supported, a public readable source repo
might be work with http, but it's not tested).

## Example
Setup the working repository:
```
$ mkdir ~/working-repo && cd ~/working-repo
$ git init
$ git remote add origin git@remote.host:path/to/source.git
$ git remote add mirror git@yourhost.com:path/to/mirror.git
$ git fetch origin
```

Thats it. Now start the script:

```
$ cd git-automated-mirror/bin
$ php git-automated-mirror.php -d ~/working-repo --remote-source origin --remote-mirror mirror
```

## Parameter

 * `-h | --help`  Print a helping message.
 * `--remote-source` The remote of the source repo.
 * `--remote-mirror` The remote of the mirror repo.
 * `--merge-branch` A local branch to merge in each branch of the source repo and tag during the process.

## Merge Branch
It's a way to merge new files to the source repo during the proccess.

Assuming the following situation:

```
$ git init
$ echo "Hello World!" > uniqueFile.txt
$ git checkout -b mergeBranch
$ git add uniqueFile.txt
$ git commit -m"add uniqueFile.txt"
```

Now add the source remote (which does not have any branch named `mergeBranch`):

```
$ git remote add origin git@remote.host:path/to/source.git
$ git fetch origin
Warning: no common commits!
…
```

With the option `--merge-branch` you can merge this branch into every other branch before pushing it to the mirror repository:

```
 php git-automated-mirror.php -d ~/working-repo --remote-source origin --remote-mirror mirror --merge-branch mergeBranch
```

## Roadmap
 * Provide a phar archive


