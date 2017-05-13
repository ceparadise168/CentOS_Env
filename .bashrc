# .bashrc

# Source global definitions
if [ -f /etc/bashrc ]; then
	. /etc/bashrc
fi

alias gl='git log --oneline --decorate --graph --color'
alias gc='git checkout'
alias gup='git remote update -p;gl'
alias gs='git status'

# Uncomment the following line if you don't like systemctl's auto-paging feature:
# export SYSTEMD_PAGER=

# User specific aliases and functions
