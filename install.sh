# Go to cuurent directory of this script
cd "$(dirname "$0")"

# check if the script is run as root
if [ "$EUID" -ne 0 ]
  then echo "Please run as root"
  exit
fi

# install dependencies: ffmpeg
# check apt
if ! [ -x "$(command -v apt)" ]; then
  echo 'Error: apt is not installed.' >&2
  echo 'Please install apt and try again.' >&2
  exit 1
fi

# update apt
sudo apt update

# check if ffmpeg is installed
if ! [ -x "$(command -v ffmpeg)" ]; then
  echo 'Error: ffmpeg is not installed.' >&2
  echo 'Installing ffmpeg...'
  sudo apt install ffmpeg
fi

# check if composer is installed
if ! [ -x "$(command -v composer)" ]; then
  echo 'Error: composer is not installed.' >&2
  echo 'Installing composer...'
  sudo apt install composer
fi

# init laravel project
cp .env.example .env

composer install
php artisan key:generate
php artisan migrate
